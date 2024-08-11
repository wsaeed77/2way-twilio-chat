<?php

namespace Chattermax\DB;

require_once __DIR__ . '/../../vendor/autoload.php'; // Include Composer's autoload file

use Chattermax\Config\Config;
use Pusher\Pusher;

class ConversationManager extends Database
{
    public function getConversationById($conversationId) {
        $stmt = $this->conn->prepare("SELECT * FROM conversations WHERE id = :id");
        $stmt->bindParam(':id', $conversationId);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getAllConversations($fromNumber)
    {
        $query = "SELECT c.*, MAX(m.created_at) as latest_message_at,
              SUM(CASE WHEN m.is_read = 0 AND m.direction = 'inbound' THEN 1 ELSE 0 END) as unread_messages_count
              FROM conversations c
              JOIN messages m ON c.id = m.conversation_id
              GROUP BY c.id
              ORDER BY latest_message_at DESC;";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getMessagesByConversationId($conversationId)
    {
        $query = "SELECT * FROM messages WHERE conversation_id = :conversation_id ORDER BY created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':conversation_id', $conversationId);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function conversationExists($fromNumber, $toNumber)
    {
        $query = "SELECT id FROM conversations
              WHERE
              (from_number = :from_number AND to_number = :to_number)
              OR
              (from_number = :to_number AND to_number = :from_number)
              LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':from_number', $fromNumber);
        $stmt->bindParam(':to_number', $toNumber);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result !== false;
    }

    public function insertConversationAndMessage($fromNumber, $toNumber, $messageBody, $direction)
    {
        $createdAt = date('Y-m-d H:i:s');
        $isRead = 0;

        // Check if the conversation already exists
        if (!$this->conversationExists($fromNumber, $toNumber)) {
            // Insert a new conversation if it does not exist
            $query = "INSERT INTO conversations (from_number, to_number, created_at, last_message_at) VALUES (:from_number, :to_number, :created_at, :created_at)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':from_number', $fromNumber);
            $stmt->bindParam(':to_number', $toNumber);
            $stmt->bindParam(':created_at', $createdAt);
            $stmt->execute();
            $conversationId = $this->conn->lastInsertId(); // Directly use lastInsertId to get the new conversation ID
        } else {
            // Fetch the existing conversation ID
            $query = "SELECT id FROM conversations WHERE (from_number = :from_number AND to_number = :to_number) OR (from_number = :to_number AND to_number = :from_number) LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':from_number', $fromNumber);
            $stmt->bindParam(':to_number', $toNumber);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $conversationId = $result['id'];
        }

        // Insert the new message
        $query = "INSERT INTO messages (conversation_id, message_body, direction, created_at, is_read) VALUES (:conversation_id, :message_body, :direction, :created_at, :is_read)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':conversation_id', $conversationId);
        $stmt->bindParam(':message_body', $messageBody);
        $stmt->bindParam(':direction', $direction);
        $stmt->bindParam(':created_at', $createdAt);
        $stmt->bindParam(':is_read', $isRead);
        $stmt->execute();

        $data = [
            'conversation_id' => $conversationId,
            'message_body' => $messageBody,
            'direction' => $direction,
            'created_at' => $createdAt,
        ];

        $options = array(
            'cluster' => Config::get('PUSHER_CLUSTER'),
            'useTLS' => true
        );

        $pusher = new Pusher(
            Config::get('PUSHER_KEY'),
            Config::get('PUSHER_SECRET'),
            Config::get('PUSHER_APP_ID'),
            $options
        );

        $pusher->trigger('twilio', 'inbound-message', $data);

        return $conversationId;
    }

    public function deleteAllMessagesInConversation($conversationId)
    {
        $query = "DELETE FROM messages WHERE conversation_id = :conversation_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':conversation_id', $conversationId);
        $stmt->execute();
    }

    public function deleteConversation($conversationId)
    {
        // First, delete all messages in the conversation
        $this->deleteAllMessagesInConversation($conversationId);

        // Then, delete the conversation itself
        $query = "DELETE FROM conversations WHERE id = :conversation_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':conversation_id', $conversationId);
        $stmt->execute();
    }

    public function insertMessage($conversationId, $messageBody, $direction, $smsSid)
    {
        $createdAt = date('Y-m-d H:i:s');
        $isRead = 0;

        $query = "INSERT INTO messages (conversation_id, message_body, message_sid, direction, created_at, is_read) VALUES (:conversation_id, :message_body, :message_sid, :direction, :created_at, :is_read)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':conversation_id', $conversationId);
        $stmt->bindParam(':message_body', $messageBody);
        $stmt->bindParam(':message_sid', $smsSid);
        $stmt->bindParam(':direction', $direction);
        $stmt->bindParam(':created_at', $createdAt);
        $stmt->bindParam(':is_read', $isRead);
        $stmt->execute();
    }

    public function searchConversations($query, $fromNumber)
    {
        $stmt = $this->conn->prepare("
        SELECT c.*, MAX(m.created_at) AS latest_message_at
        FROM conversations c
        LEFT JOIN messages m ON c.id = m.conversation_id
        WHERE c.to_number LIKE :query
        GROUP BY c.id
    ");
        $stmt->execute([
            'query' => "%$query%"
        ]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function markMessageAsRead($messageId) {
        $query = "UPDATE messages SET is_read = 1 WHERE id = :message_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':message_id', $messageId);
        return $stmt->execute();
    }
}

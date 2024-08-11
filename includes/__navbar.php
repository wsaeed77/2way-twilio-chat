<nav class="navbar navbar-expand-lg navbar-light bg-light ">
    <div class="container d-flex justify-content-between">
        <div></div> <!-- Placeholder for other navbar content -->
        <div class="d-flex gap-2 m-3 ms-auto">
<!--            <span class="fa fa-shield me-2" aria-hidden="true"></span>-->
            <span style="cursor: pointer" class="far fa-trash-alt" aria-hidden="true" data-conversation-id="${conversation.id}"></span>
        </div>
        <div class="gap-3">
<!--        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#composeModal">Compose</button>-->
        <!-- Logout Button -->
        <a href="/logout.php" class="btn btn-secondary me-1">Logout</a>
        </div>
    </div>
</nav>
<?php include 'includes/__modal.php'; ?>
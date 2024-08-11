resource "aws_db_subnet_group" "main" {
  name       = "chattermax db - production"
  subnet_ids = [data.aws_subnet.main.id, data.aws_subnet.secondary.id]
}

resource "aws_security_group" "allow_all" {
  name        = "allow_all"
  description = "Allow all inbound traffic"
  vpc_id      = data.aws_vpc.main.id
  tags = {
    Name = "Chattermax DB"
  }

  ingress {
    from_port   = 3306
    to_port     = 3306
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }
}

resource "aws_db_instance" "prod_database" {
  publicly_accessible    = true
  identifier             = "chattermax-db"
  db_name                = "chattermax"
  allocated_storage      = 20
  max_allocated_storage  = 100 // for autoscaling the storage
  storage_type           = "gp2"
  engine                 = "mysql"
  engine_version         = "8.0"
  instance_class         = "db.t3.micro"
  username               = var.db_username
  password               = var.db_default_password
  parameter_group_name   = "default.mysql8.0"
  skip_final_snapshot    = true
  db_subnet_group_name   = aws_db_subnet_group.main.name
  vpc_security_group_ids = [aws_security_group.allow_all.id]
}
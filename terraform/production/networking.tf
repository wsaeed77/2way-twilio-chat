data "aws_subnet" "main" {
  filter {
    name   = "tag:Name"
    values = ["Main"]
  }
}

data "aws_subnet" "secondary" {
  filter {
    name   = "tag:Name"
    values = ["Secondary"]
  }
}

data "aws_vpc" "main" {
  filter {
    name   = "tag:Name"
    values = ["main"]
  }
}
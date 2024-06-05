<?php

namespace Root\P5\models;

use DateTime;

class User
{
    public int $id;
    public string $username;
    public string $password;
    public string $email;
    public bool $isConfirmed;
    public DateTime $createdAt;
}
<?php

namespace Root\P5\models;

use DateTime;

class Post
{
    public int $id;
    public string $title;
    public string $chapo;
    public string $content;
    public string $author;
    public DateTime $updatedAt;
}

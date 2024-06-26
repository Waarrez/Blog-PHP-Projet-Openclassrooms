<?php

namespace Root\P5\Services;

use Exception;
use Root\P5\models\CommentRepository;
use Root\P5\models\UsersRepository;

class AdminService
{
    private UsersRepository $usersRepository;
    private CommentRepository $commentRepository;

    public function __construct(UsersRepository $usersRepository, CommentRepository $commentRepository)
    {
        $this->usersRepository = $usersRepository;
        $this->commentRepository = $commentRepository;
    }

    /**
     * @throws Exception
     */
    public function getUnapprovedUsers(): array
    {
        return $this->usersRepository->getUserNotApproved();
    }

    /**
     * @throws Exception
     */
    public function getUnconfirmedComments(): array
    {
        return $this->commentRepository->getUnconfirmedComments();
    }

    /**
     * @throws Exception
     */
    public function approveComment(int $id): bool
    {
        return $this->commentRepository->confirmComment($id);
    }

    /**
     * @throws Exception
     */
    public function approveUser(int $id): bool
    {
        return $this->usersRepository->confirmUser($id);
    }
}

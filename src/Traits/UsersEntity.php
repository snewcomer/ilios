<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\UserInterface;

/**
 * Class UsersEntity
 */
trait UsersEntity
{
    /**
     * @param Collection $users
     */
    public function setUsers(Collection $users)
    {
        $this->users = new ArrayCollection();

        foreach ($users as $user) {
            $this->addUser($user);
        }
    }

    /**
     * @param UserInterface $user
     */
    public function addUser(UserInterface $user)
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }
    }

    /**
     * @param UserInterface $user
     */
    public function removeUser(UserInterface $user)
    {
        $this->users->removeElement($user);
    }

    /**
    * @return UserInterface[]|ArrayCollection
    */
    public function getUsers()
    {
        return $this->users;
    }
}

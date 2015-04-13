<?php
// TODO: add namespace support
use Mouf\Security\Userdao\AbstractUserEntity;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

/**
 * @Entity
 * @Table(name="users")
 */

class UserEntity extends AbstractUserEntity {
}
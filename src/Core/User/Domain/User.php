<?php

namespace App\Core\User\Domain;

use App\Common\EventManager\EventsCollectorTrait;
use App\Core\User\Domain\Exception\UserException;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\Validation;


/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User
{
    use EventsCollectorTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=300, nullable=false)
     */
    private string $email;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private bool $active;

    public function __construct(string $email)
    {
        $this->id = null;
        $this->setEmail($email);
        $this->active = false;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    private function setEmail(string $email)
    {
        $builder = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(
                new ConstraintValidatorFactory(
                    [
                        Assert\EmailValidator::class => new Assert\EmailValidator(
                        Assert\Email::VALIDATION_MODE_HTML5)
                    ]
                )
            );

        $violations = $builder->getValidator()->validate($email, [
        new Assert\NotBlank(),
            new Assert\Email(),
        ]);

        if (count($violations) > 0) {
            throw new UserException('Aby utworzyć użytkownika podaj prawidłowy mail');
        }

        $this->email = $email;
    }
}

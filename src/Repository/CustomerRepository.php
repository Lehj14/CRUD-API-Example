<?php

namespace App\Repository;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{
    /** @var EntityManagerInterface $manager */
    private $manager;

    /**
     * CustomerRepository constructor.
     *
     * @param ManagerRegistry $registry
     * @param EntityManagerInterface $manager
     */
    public function __construct
    (
        ManagerRegistry $registry,
        EntityManagerInterface $manager
    )
    {
        parent::__construct($registry, Customer::class);
        $this->manager = $manager;
    }

    /**
     * Add customer.
     *
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param int $phoneNumber
     */
    public function addCustomer(string $firstName, string $lastName, string $email, int $phoneNumber)
    {
        $newCustomer = new Customer();

        $newCustomer
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setEmail($email)
            ->setPhoneNumber($phoneNumber);

        $this->manager->persist($newCustomer);
        $this->manager->flush();
    }

    /**
     * Update the customer
     *
     * @param Customer $customer
     *
     * @return Customer
     */
    public function updateCustomer(Customer $customer): Customer
    {
        $this->manager->persist($customer);
        $this->manager->flush();

        return $customer;
    }

    /**
     * Remove the customer.
     *
     * @param Customer $customer
     */
    public function removeCustomer(Customer $customer)
    {
        $this->manager->remove($customer);
        $this->manager->flush();
    }
}

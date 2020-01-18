<?php

namespace App\Controller;

use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CustomerController extends AbstractController
{

    /** @var CustomerRepository CustomerRepository  */
    private $customerRepository;

    /**
     * CustomerController constructor.
     *
     * @param CustomerRepository $customerRepository
     */
    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * @Route("/customer", name="customer")
     */
    public function index()
    {
        return $this->render('customer/index.html.twig', [
            'customers' => $this->getAllCustomersAction(),
        ]);
    }

    /**
     * Add new customer details
     *
     * @Route("/customers/add", name="add_customer", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function newAction(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $firstName = $data['firstName'];
        $lastName = $data['lastName'];
        $email = $data['email'];
        $phoneNumber = $data['phoneNumber'];

        if (empty($firstName) || empty($lastName) || empty($email) || empty($phoneNumber)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        //validation should be handled on front end for phone number and also for email (need to be unique.)
        //think if its submitted and validation then check if the email already exist... search online which is better to use.
        $this->customerRepository->addCustomer($firstName, $lastName, $email, $phoneNumber);

        return new JsonResponse(['status' => 'success' , 'message' => 'Customer created!'], Response::HTTP_CREATED);
    }

    /**
     * Retrieve customer details by customer id.
     *
     * @Route("/customers/{id}", name="get_one_customer", methods={"GET"})
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function customerDetailsAction(int $id): JsonResponse
    {
        $customerData = $this->customerRepository->findOneBy(['id' => $id]);

        if (!empty($customerData)) {
            $data = [
                'id' => $customerData->getId(),
                'firstName' => $customerData->getFirstName(),
                'lastName' => $customerData->getLastName(),
                'email' => $customerData->getEmail(),
                'phoneNumber' => $customerData->getPhoneNumber(),
            ];
        } else {
            return new JsonResponse(['status' => 'failed', 'message' => 'No data found for customer id: ' . $id], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * Get all customers.
     *
     * @Route("/customers", name="get_all_customers", methods={"GET"})
     */
    public function getAllCustomersAction(): JsonResponse
    {
        $customers = $this->customerRepository->findAll();
        $data = [];

        if (!empty($customers)) {
            foreach ($customers as $customer) {
                $data[] = [
                    'id' => $customer->getId(),
                    'firstName' => $customer->getFirstName(),
                    'lastName' => $customer->getLastName(),
                    'email' => $customer->getEmail(),
                    'phoneNumber' => $customer->getPhoneNumber(),
                ];
            }
        } else {
            return new JsonResponse(['status' => 'failed', 'message' => 'There are no customers data'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * Update the customer detail.
     *
     * @Route("/customers/update/{id}", name="update_customer", methods={"PUT"})
     *
     * @param int $id
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function updateAction(int $id, Request $request): JsonResponse
    {
        $customer = $this->customerRepository->findOneBy(['id' => $id]);

        if (empty($customer)) {
            return new JsonResponse(['status' => 'failed', 'message' => 'There is an error updating customer id: ' . $id], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        empty($data['firstName']) ? true : $customer->setFirstName($data['firstName']);
        empty($data['lastName']) ? true : $customer->setLastName($data['lastName']);
        empty($data['email']) ? true : $customer->setEmail($data['email']);
        empty($data['phoneNumber']) ? true : $customer->setPhoneNumber($data['phoneNumber']);

        $updatedCostumer = $this->customerRepository->updateCustomer($customer);
        $jsonContent = $this->serialize($updatedCostumer, 'json');

        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }

    /**
     * Delete customer.
     *
     * @Route("/customers/delete/{id}", name="delete_customer", methods={"DELETE"})
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function deleteAction(int $id): JsonResponse
    {
        $customer = $this->customerRepository->findOneBy(['id' => $id]);

        if (!empty($customer)) {
            return new JsonResponse(['status' => 'failed', 'message' => 'There is a problem deleting the customer.'], Response::HTTP_NOT_FOUND);
        }

        $this->customerRepository->removeCustomer($customer);

        return new JsonResponse(['status' => 'Customer deleted'], Response::HTTP_NO_CONTENT);
    }

    /**
     * Serialize data.
     *
     * @param $data
     * @param $format
     *
     * @return string
     */
    public function serialize($data, $format)
    {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->serialize($data, $format);

    }
}

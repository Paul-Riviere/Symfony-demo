<?php

namespace App\Controller;

use App\Model\UserDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Context\Encoder\CsvEncoderContextBuilder;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ExampleController extends AbstractController
{
    #[Route('/example/MapQueryParameter', name: 'app_example_mapQueryParameter')]
    public function mapQueryParameter(
        #[MapQueryParameter] string $firstName,
        #[MapQueryParameter] string $lastName,
        #[MapQueryParameter] int $age,
    ): JsonResponse
    {
        return $this->json([
            'firstName' => $firstName,
            'lastName' => $lastName,
            'age' => $age,
        ]);
    }

    #[Route('/example/MapQueryString', name: 'app_example_mapQueryString')]
    public function mapQueryString(
        #[MapQueryString(
            validationFailedStatusCode: Response::HTTP_UNPROCESSABLE_ENTITY
        )] UserDTO $userDto,
    ): JsonResponse
    {
        return $this->json([
            'user' => $userDto
        ]);
    }

    #[Route('/example/MapRequestPayload', name: 'app_example_mapRequestPayload')]
    public function mapRequestPayload(
        #[MapRequestPayload(
            acceptFormat: 'json',
            validationFailedStatusCode: Response::HTTP_UNPROCESSABLE_ENTITY
        )] UserDTO $userDto,
    ): JsonResponse
    {
        return $this->json([
            'user' => $userDto
        ]);
    }

    #[Route('/example/serialize', name: 'app_example_serialize')]
    public function serialize(SerializerInterface $serializer): JsonResponse
    {
        $userDTO = new UserDTO("first", "last", 20);
        $serializedUserDTO= $serializer->serialize($userDTO, "json", [DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s']);
        return $this->json([
            'serialized' => $serializedUserDTO,
            'raw' => $userDTO
        ]);
    }

    #[Route('/example/serializewithcontext', name: 'app_example_serializewithcontext')]
    public function serializewithcontext(SerializerInterface $serializer): JsonResponse
    {
        $userDTO = new UserDTO("first", "last", 20);

        $initialContext = [];
        $contextBuilder = (new ObjectNormalizerContextBuilder())
            ->withContext($initialContext)
            ->withGroups(['list_names']);
        $contextBuilder = (new CsvEncoderContextBuilder())
            ->withContext($contextBuilder)
            ->withDelimiter(';');

        $serializedUserDTO = $serializer->serialize($userDTO, 'csv', $contextBuilder->toArray());

        return $this->json([
            'serialized' => $serializedUserDTO,
            'raw' => $userDTO
        ]);
    }

    #[Route('/example/serializewithcontextbis', name: 'app_example_serializewithcontextbis')]
    public function serializewithcontextbis(SerializerInterface $serializer): JsonResponse
    {
        $userDTO = new UserDTO("first", "last", 20);

        $initialContext = [
            DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s'
        ];
        $contextBuilder = (new ObjectNormalizerContextBuilder())
            ->withContext($initialContext)
            ->withGroups(['list_names_with_birthdate']);
        $contextBuilder = (new CsvEncoderContextBuilder())
            ->withContext($contextBuilder)
            ->withDelimiter(';');

        $serializedUserDTO = $serializer->serialize($userDTO, 'csv', $contextBuilder->toArray());

        return $this->json([
            'serialized' => $serializedUserDTO,
            'raw' => $userDTO
        ]);
    }

    #[Route('/example/test', name: 'app_example_test')]
    public function test(): JsonResponse
    {
        $names = [
            'lorem',
            'ipsum',
            'consectetur',
            'adipiscing',
            'incididunt',
            'labore',
            'voluptate',
            'dolore',
            'pariatur',
        ];

        return $this->json([
            'name' => $names[array_rand($names)]
        ]);
    }
}

<?php


namespace App\Validators;


use App\Entity\Item;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ItemValidator
 * @package App\Validators
 */
class ItemValidator
{
    private ValidatorInterface $validator;

    /**
     * ItemValidator constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Validates
     *
     * @param Item $item
     * @return Response
     */
    public function validateItem(Item $item)
    {
        // check object for validation errors
        $errors = $this->validator->validate($item);

        if (count($errors) > 0) {
            // generate well formatted string of errors
            $errorsString = (string) $errors;

            return new Response($errorsString);
        }

        return new Response('The author is valid! Yes!');
    }
}
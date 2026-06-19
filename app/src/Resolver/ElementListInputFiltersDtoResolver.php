<?php

/**
 * Element list input filters DTO resolver.
 */

namespace App\Resolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use App\Dto\ElementListInputFiltersDto;

/**
 * Class ElementListInputFiltersDtoResolver.
 */
class ElementListInputFiltersDtoResolver implements ValueResolverInterface
{
    /**
     * Resolves the ElementListInputFiltersDto argument.
     *
     * @param Request          $request  HTTP request
     * @param ArgumentMetadata $argument Argument metadata
     *
     * @return iterable<ElementListInputFiltersDto> Resolved argument
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();

        if (!$argumentType || !is_a($argumentType, ElementListInputFiltersDto::class, true)) {
            return [];
        }

        $categoryId = $request->query->get('categoryId');
        $tagId = $request->query->get('tagId');

        return [
            new ElementListInputFiltersDto(
                null !== $categoryId ? (int) $categoryId : null,
                null !== $tagId ? (int) $tagId : null
            ),
        ];
    }
}

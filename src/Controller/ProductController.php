<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    #[Route('/products', name: 'app_product')]
    public function listProduct(): Response
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
            'title' => 'Liste des produits',
        ]);
    }

    #[Route('/product/{id}', name: 'product_view')]
    public function viewProduct($id): Response
    {
        return $this->render('product/[id].html.twig', [
            'id' => $id,
        ]);


    }
}

<?php
namespace App\Controller;

use App\Form\TestType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController {
    #[Route(path:["/test", "/t"], name:"test_app")]
    public function index(Request $request): Response {
        $form = $this->createForm(TestType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
        }
        return $this->render("test/index.html.twig", [
            "form" => $form
        ]);
    }
}
?>
<?php
namespace App\Controller;

use App\Entity\Login;
use App\Form\LoginType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController 
{
    #[Route(path:"/", name:"home", methods:["GET", "POST"])]
    public function index(Request $request, EntityManagerInterface $db): Response
    {
        try {
            /*
            $login = new login;
            $login->setDescription("This is a symfony project in progress");
            $login->setName("matheu");
            $login->setEmail("matheusviaira160@gmail.com");
            $login->setPasswd(password_hash("ratak1413", PASSWORD_BCRYPT));
            $db->persist($login);
            $db->flush();
             */

            $login_form = $this->createForm(LoginType::class);

            $login_form->handleRequest($request);

            if ($login_form->isSubmitted() && $login_form->isValid()) {
                $db->persist($login_form->getData());
                $db->flush();
            }
            return $this->render(view:"home/index.html.twig", parameters:[
                "login_form" => $login_form
            ], response:new Response(status:Response::HTTP_OK));
        } catch (\Exception $th) {
            // throw $th;
            return $this->render(view:"error/index.html.twig", parameters:[
                "description" => $th->getMessage()
            ], response:new Response(status:Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }
}
?>

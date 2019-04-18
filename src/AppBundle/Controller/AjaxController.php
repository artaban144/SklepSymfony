<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\JsonResponse;

class AjaxController extends Controller
{
    /**
     * @Route("/ajax", name="ajax")
     */

     public function indexAction(Request $request)
     {
         if($request->request->get('some_var_name')){
             //make something curious, get some unbelieveable data
             $arrData = ['output' => 'here the result which will appear in div'];
             return new JsonResponse($arrData);
         }

         return $this->render('ajaxtest.html.twig');
     }
}

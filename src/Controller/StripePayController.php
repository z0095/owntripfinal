<?php

namespace App\Controller;

use App\Service\CartService;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripePayController extends AbstractController
{
    #[Route('/stripe/pay', name: 'app_stripe_pay')]
    public function index(CartService $cs): Response
    {
        $fullCart= $cs->getCartWithData();

        $line_items=[];
            foreach ($fullCart as $item ) {
                $line_items[]=[
                    'price_data'=>[
                        'unit_amount'=>$item['activity']->getPrice()*100, 'currency'=>'EUR',
                        'product_data'=>['name'=>$item['activity']->getName()
                        ]
                    ],
                    'quantity'=>$item['quantity']


                ];

            }

          Stripe::setApiKey('sk_test_51Nwh3ZLErOQOxgsTGU2a5HlwAqzd5CLc5lP3zTE5m3ez1fDVTC0NJj44hpSRsqnUAdNilBjs17LliFkuptQAgCzm00AUW2t6ZB');
            $session=Session::create([
                'success_url'=>'https://ibrahim.lock.cezdigit.com/commande/success',
                //'success_url'=>'http://127.0.0.1:8000/commande/success',
                'cancel_url'=>'https://ibrahim.lock.cezdigit.com/commande/wishList',
                //'cancel_url'=>'http://127.0.0.1:8000/commande/success',
                'payment_method_types'=>['card'],
                'line_items'=>$line_items,
                'mode'=>'payment'
            ]);
            return $this->redirect($session->url, 303);


        



      
    }

    #[Route('/commande/{success}', name: 'commande')]
    public function commande(CartService $cartService,$success=null): Response
    {
        if ($success) {
            $this->addFlash('success','Merci pour votre confiance');
            $cartService->destroy();
            return $this->redirectToRoute('app_front');

        }else{
        $this->addFlash('danger','un probleme est servenu merci de reiterer votre paiement');
        return $this->redirectToRoute('app_front');
        }
    
        
        
    }






}

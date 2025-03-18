<?php

namespace App\Controller\Front;

use App\Entity\Testimonial;
use App\Form\TestimonialFormType;
use App\Service\HeaderDataProvider;
use App\Repository\HomePageRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TestimonialRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TestimonialController extends AbstractController
{
    public function __construct(
        private HomePageRepository $homePage, 
        private HeaderDataProvider $headerDataProvider,
        private PaginatorInterface $paginator,
        private TestimonialRepository $testimonialRepo,
        private EntityManagerInterface $em,
    )
    {

    }

    #[Route('/feane/testimonial', name: 'app_testimonial')]
    public function index(Request $request): Response
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();

        $testimonials = $this->testimonialRepo->findBy(['isActive' => true]);

        $pagination = $this->paginator->paginate(
            $testimonials,
            $request->query->getInt('page', 1),
            6
        );

        $pagination->setTemplate('front/partials/pagination.html.twig');


        $data = array_merge($headerData, [
            'homePages' => $homePages,
            'testimonials' => $pagination
        ]);

        return $this->render('front/testimonial/index.html.twig',$data);
    }

    #[Route('/feane/testimonial-list', name: 'app_testimonial_list')]
    #[IsGranted("ROLE_USER")]
    public function list(Request $request): Response
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();

        $user = $this->getUser();

        $testimonials = $this->testimonialRepo->findBy(['user' => $user]);

        $pagination = $this->paginator->paginate(
            $testimonials,
            $request->query->getInt('page', 1),
            6
        );

        $pagination->setTemplate('front/partials/pagination.html.twig');


        $data = array_merge($headerData, [
            'homePages' => $homePages,
            'testimonials' => $pagination
        ]);

        return $this->render('front/testimonial/liste.html.twig',$data);
    }

    #[Route('/feane/testimonial-add', name: 'app_testimonial_add')]
    #[IsGranted("ROLE_USER")]
    public function add(Request $request): Response
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();

        $testimonial = new Testimonial();

        $form = $this->createForm(TestimonialFormType::class, $testimonial);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $date = new \DateTime();
            $user = $this->getUser();

            $comment = $form->get('comment')->getData();

            $testimonial->setCreatedAt($date)
                        ->setUser($user)
                        ->setComment($comment)
                        ->setIsActive(false);

            $this->em->persist($testimonial);
            $this->em->flush();

            return $this->redirectToRoute('app_testimonial_list');
            
        }

        $data = array_merge($headerData, [
            'homePages' => $homePages,
            'form' => $form->createView()
        ]);

        return $this->render('front/testimonial/form.html.twig',$data);
    }

    #[Route('/feane/testimonial-edit/{id}', name: 'app_testimonial_edit')]
    #[IsGranted("ROLE_USER")]
    public function edit(Request $request): Response
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();

        $user = $this->getUser();
        $testimonial = $this->testimonialRepo->findOneByUser($user);

        $form = $this->createForm(TestimonialFormType::class, $testimonial);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $this->em->flush();

            return $this->redirectToRoute('app_testimonial_list');
            
        }

        $data = array_merge($headerData, [
            'homePages' => $homePages,
            'form' => $form->createView()
        ]);

        return $this->render('front/testimonial/form.html.twig',$data);
    }
}

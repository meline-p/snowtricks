<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\UserTrick;
use App\Form\TricksFormType;
use App\Repository\CategoryRepository;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/tricks', name: 'app_tricks_')]
class TricksController extends AbstractController
{
    #[Route('/categories/{category_slug}', name: 'index')]
    public function index(
        ?string $category_slug,
        CategoryRepository $categoryRepository,
        TrickRepository $trickRepository,
        Request $request
    ): Response {
        // Check if the category_slug parameter is present
        if ('all' === $category_slug) {
            $tricks = $trickRepository->findAll();
            $category_slug = 'all';
        } else {
            $categorySelected = $categoryRepository->findOneBy(['slug' => $category_slug]);
            $category_slug = $category_slug;
            $tricks = $trickRepository->findByCategory($categorySelected);
        }

        $categories = $categoryRepository->findAll();

        // Get the page number from the URL query parameters
        $page = $request->query->getInt('page', 1);

        // Retrieve paginated tricks based on the selected category
        $tricks = $trickRepository->findTricksPaginated($page, $category_slug, 10);

        return $this->render('tricks/index.html.twig', [
            'categories' => $categoryRepository->findBy([], ['categoryOrder' => 'asc']),
            'tricks' => $tricks,
            'categories' => $categories,
            'category_slug' => $category_slug,
        ]);
    }

    #[Route('/details/{slug}', name: 'details')]
    public function details(
        Trick $trick,
        ImageRepository $imageRepository,
    ): Response {
        $images = $imageRepository->findBy(['trick' => $trick]);

        return $this->render('tricks/details.html.twig', [
            'trick' => $trick,
            'images' => $images,
        ]);
    }

    #[Route('/ajouter', name: 'add')]
    public function add(
        CategoryRepository $categoryRepository, 
        Request $request, 
        EntityManagerInterface $em, 
        SluggerInterface $slugger,
        PictureService $pictureService
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Create new instances of Trick and UserTrick
        $trick = new Trick();
        $userTrick = new UserTrick();
       
        /** @var User $user */
        $user = $this->getUser();
   
        // Create the form for adding a trick
        $trickForm = $this->createForm(TricksFormType::class, $trick);

        // Handle form submission
        $trickForm->handleRequest($request);
        
        // Check if form is submitted and valid
        if($trickForm->isSubmitted() && $trickForm->isValid()){

            // Generate slug for the trick's name
            $slug = strtolower($slugger->slug($trick->getName()));
            $trick->setSlug($slug);

            $description = $trick->getDescription();
            $trick->setDescription($description);

            $em->persist($trick);
            $em->flush();

            // Retrieve the images
            $images = $trickForm->get('images')->getData();

            foreach ($images as $image){
                // Define the destination folder
                $folder = 'tricks';
            
                $img = new Image();
                // Generate a unique file name
                $fileName = uniqid() . '_' . $trick->getId() . '.' . $image->getClientOriginalExtension();
    
                // Call the add service with the specific file name
                $fichier = $pictureService->add($image, $folder, 300, 300, $fileName);
                $extension = pathinfo($fichier, PATHINFO_EXTENSION);

                $img->setName($fileName);
                $img->setExtension($extension);
                $trick->addImage($img);
            }
            
            // Persist and flush
            $em->persist($trick);
            $em->flush();

            $userTrick->setOperation('create');
            $userTrick->setDate(new \DateTime());
            $userTrick->setUser($user);
            $userTrick->setTrick($trick);

            $em->persist($userTrick);
            $em->flush();

            $this->addFlash('success', 'Figure ajoutée avec succès');

            return $this->redirectToRoute('app_tricks_details', ['slug' => $slug]);

        }

        return $this->render('tricks/add.html.twig', [
            'trickForm' => $trickForm->createView(),
        ]);
    }

    #[Route('/modifier/{slug}', name: 'edit')]
    public function edit(
        Trick $trick,
        ImageRepository $imageRepository,
        Request $request, 
        EntityManagerInterface $em, 
        SluggerInterface $slugger,
        PictureService $pictureService
    ): Response {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $images = $imageRepository->findBy(['trick' => $trick]);

        /** @var User $user */
        $user = $this->getUser();

        // Create the form for editing the trick
        $trickForm = $this->createForm(TricksFormType::class, $trick);

        // Handle form submission
        $trickForm->handleRequest($request);
        
        // Check if form is submitted and valid
        if($trickForm->isSubmitted() && $trickForm->isValid()){
            // Generate slug for the trick's name
            $slug = strtolower($slugger->slug($trick->getName()));
            $trick->setSlug($slug);

            $description = $trick->getDescription();
            $trick->setDescription($description);

            $em->persist($trick);
            $em->flush();

            // retrieve the images
            $images = $trickForm->get('images')->getData();

            foreach ($images as $image){
                // define the destination folder
                $folder = 'tricks';
             
                $img = new Image();
                // Generate a unique file name
                $fileName = uniqid() . '_' . $trick->getId() . '.' . $image->getClientOriginalExtension();
     
                // Call the add service with the specific file name
                $fichier = $pictureService->add($image, $folder, 300, 300, $fileName);
                $extension = pathinfo($fichier, PATHINFO_EXTENSION);
 
                $img->setName($fileName);
                $img->setExtension($extension);
                $trick->addImage($img);
            }
             
             // Persist and flush
             $em->persist($trick);
             $em->flush();

            $userTrick = new UserTrick();
            $userTrick->setOperation('update');
            $userTrick->setDate(new \DateTime());
            $userTrick->setUser($user);
            $userTrick->setTrick($trick);

            $em->persist($userTrick);
            $em->flush();

            $this->addFlash('success', 'Figure modifiée avec succès');

            return $this->redirectToRoute('app_tricks_details', ['slug' => $slug]);
        }

        return $this->render('tricks/edit.html.twig', [
            'trick' => $trick,
            'images' => $images,
            'trickForm' => $trickForm->createView(),
        ]);
    }

    #[Route('/supprimer/{slug}', name: 'delete')]
    public function delete(Trick $trick,): Response {

        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('tricks/edit.html.twig', [
            'trick' => $trick,
        ]);
    }

    #[Route('/supprimer/image/{id}', name: 'delete_image', methods:['DELETE'])]
    public function deleteImage(
        Image $image, 
        Request $request, 
        EntityManagerInterface $em,
        PictureService $pictureService
        ): Response {

        $this->denyAccessUnlessGranted('ROLE_USER');

        // Retrieve request content
        $data = json_decode($request->getContent(), true);

        if($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])){
            // CSRF token is valid
            // Retrieve image name
            $name = $image->getName();

            if($pictureService->delete($name, 'tricks', 300, 300)){
                // Delete image from database
                $em->remove($image);
                $em->flush();

                return new JsonResponse(['success' => true], 200);
            }
            // Deletion failed
            return new JsonResponse(['error' => 'Erreur de suppression'], 400);
        }
        
        return new JsonResponse(['error' => 'Token invalide'], 400);
    }
}

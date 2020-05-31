<?php
// api/src/Controller/ControlImage.php
namespace App\Controller;


use App\Entity\MediaObject;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class ControlImage
{
    
    

    public function __invoke(Request $request, UserRepository $userRepo): User
    {
         $uploadedFile = $request->files->get('file');
        
        if (!$uploadedFile) {
            throw new BadRequestHttpException("Veuillez inserer une image SVP...");
        }else{ 

        $extension= $uploadedFile->guessExtension();
      
        if( ($extension == "png") ||( $extension == "jpeg") || ( $extension == "jpg") )
        {
            //$image=file_get_contents($_FILES['image']['tmp_name']);
            //recuperation de l'url URI du route de chargement de l'image 
           
            $url=$_SERVER["REQUEST_URI"];
        
          //conversion de l'url URI en Array 
            $tabUrl=explode("/", $url);

          //recuperation de l'id de l'url URI 
            $id=$tabUrl[4];
          //recuperation des donnees de l'utilisateur par l'id
            $data= $userRepo->find($id);

    $data->setImageProfile($uploadedFile);
            
            return $data;
        }else{
            throw new BadRequestHttpException("Les images doivent être au format JPG ou JPEG ou PNG");
        }
    }
    
   

}

}

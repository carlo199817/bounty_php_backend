<?php

    $requestID = null;
    if (isset($uri[2])) {
        $requestID  = (int) $uri[2];
    } 
    if (isset($_POST["name"])){ 
        $name = $_POST["name"];
    }else { 
        $name = null; 
    }  

    $requestMethod = $_SERVER["REQUEST_METHOD"];
    $controller = new ProductController($requestMethod,$requestID,$name);
    $controller->processRequest();
    
class ProductController {
    private $requestMethod;
    private $requestID;
    private $name;
    
    public function __construct($requestMethod,$requestID,$name)
    {
        $this->name = $name;
        $this->requestMethod = $requestMethod;
        $this->requestID = $requestID;
    }
    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->requestID) {
                    $response = $this->getProduct();
                } else {
                    $response = $this->getProductList();
                };
                break;
            case 'POST':
                $response = $this->postProduct();
                break;
            case 'PATCH':
                $response = $this->patchProduct();
                break;
            case 'DELETE':
                $response = $this->deleteProduct();
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }
    
    private function getProductList()
    {
        require "bootstrap.php";       
        $repositoryList = [];
        $productRepository = $entityManager->getRepository('Product')->findAll();
        $products = $productRepository;
    
        echo header('Content-Type: application/json; charset=utf-8');
        $response['status_code_header'] = 'HTTP/1.1 200 OK';


        foreach ($products as $product) {
           array_push($repositoryList, ['id'=>$product->id(),"name"=>$product->name(),"address"=>["country"=>$product->getAddress()->getCountry(),
           "street"=>$product->getAddress()->getStreet(),"city"=>$product->getAddress()->getCity(),  "postalCode"=>$product->getAddress()->getPostal()]]); 
        }
        $response['body'] = json_encode($repositoryList);
        return $response;
    }

    private function getProduct()
    {
        require "bootstrap.php";
        $repositoryList = [];
        $productRepository = $entityManager->getRepository('Product');
        $products = $productRepository->find($this->requestID);
        if ($products) {
            foreach ([$products] as $product) {
                array_push($repositoryList, ['id'=>$product->id(),"name"=>$product->name(),"address"=>["country"=>$product->getAddress()->getCountry(),
                "street"=>$product->getAddress()->getStreet(),"city"=>$product->getAddress()->getCity(),  "postalCode"=>$product->getAddress()->getPostal()]]); 
             }
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($repositoryList[0]);
        } else {
            $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
            $response['body'] = json_encode(['Message'=>'ID NOT FOUND']);
        };
 
        return $response;
    }

    private function postProduct()
    {
        require_once "bootstrap.php";
        $repositoryList = [];
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        $product = new Product();
        $product->setName($input['name']);
        $address = $entityManager->find(Address::class,$input['address']); 

        $product->setAddress($address);
        $entityManager->persist($product);
        $entityManager->flush();

        $productRepository = $entityManager->getRepository('Product');
        $products = $productRepository->find($product->id());
        foreach ([$products] as $product) {
            array_push($repositoryList, ['id'=>$product->id(),"name"=>$product->name(),"address"=>["country"=>$product->getAddress()->getCountry(),
            "street"=>$product->getAddress()->getStreet(),"city"=>$product->getAddress()->getCity(),  "postalCode"=>$product->getAddress()->getPostal()]]); 
         }
        $response['status_code_header'] = 'HTTP/1.1 201 created';
        $response['body'] = json_encode($repositoryList[0]);
        return $response;

    }
    
    private function patchProduct()
    {
       $input = (array) json_decode(file_get_contents('php://input'), TRUE);
    require_once "bootstrap.php";
        $product = $entityManager->find('product', $this->requestID);
        if ($product) {
            $product->setName($input['name']); 
            $entityManager->flush();
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode(['id'=>$this->requestID,'name'=>$input['name']]);
        }else{
            $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
            $response['body'] = json_encode(['Message'=>'ID NOT FOUND']);
              exit(1);
        }

    return $response;
    }
    private function deleteProduct()
    {
            require_once "bootstrap.php";
            $product = $entityManager->find('product', $this->requestID);
            if($product){
                $entityManager->remove($product);
                $entityManager->flush();
                $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
                $response['body'] = json_encode(['Message'=>'Deleted']);
            }else{
                $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
                $response['body'] = json_encode(['Message'=>'ID NOT FOUND']);
            } 
        return $response;
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = json_encode(["Message"=>"Method not allowed"]);
        return $response;
    }
}


?>
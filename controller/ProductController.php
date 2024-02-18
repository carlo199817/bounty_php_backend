<?php

    $requestID = null;
    if (isset($uri[2])) {
        $requestID  = (int) $uri[2];
    } 
  /*  if (isset($_POST["name"])){ 
        $name = $_POST["name"];
    }else*/  
    if (isset($_PATCH["name"])){ 
        $name = $_PATCH["name"];
    }else { 
       echo json_encode($_PATCH["name"]);
        $name = null; 
    }  

   echo json_encode($name);
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
           array_push($repositoryList, ['id'=>$product->id(),"name"=>$product->name()]); 
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
                array_push($repositoryList, ['id'=>$product->id(),"name"=>$product->name()]); 
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
        $product = new Product();
        $product->setName($this->name);
        $entityManager->persist($product);
        $entityManager->flush();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(['id'=>$product->id(),'name'=>$product->name()]);
        return $response;

    }
    
    private function patchProduct()
    {
  
        require_once "bootstrap.php";
        $product = $entityManager->find('product', $this->requestID);
        if ($product) {
            $product->setName("SDS"); 
            $entityManager->flush();
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode(['Message'=>'SUCCESS']);
        }else{
            $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
            $response['body'] = json_encode(['Message'=>'ID NOT FOUND']);
              exit(1);
        }

    return $response;
    }

    private function deleteProduct()
    {
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = "This is Delete $this->requestID";
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
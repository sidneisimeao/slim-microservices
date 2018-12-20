<?php

use App\Models\Entity\Book;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

require './bootstrap.php';

/**
 * Lista de todos os livros
 * @request curl -X GET http://localhost:8000/book
 */
$app->get('/books', function (Request $request, Response $response) use ($app) {
    $entityManager = $this->get('em');
    $booksRepository = $entityManager->getRepository('App\Models\Entity\Book');
    $books = $booksRepository->findAll();
    $return = $response->withJson($books, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

/**
 * Retornando mais informações do livro informado pelo id
 * @request curl -X GET http://localhost:8000/book/1
 */
$app->get('/books/{id}', function (Request $request, Response $response) use ($app) {
    $route = $request->getAttribute('route');
    $id = $route->getArgument('id');
    $entityManager = $this->get('em');
    $booksRepository = $entityManager->getRepository('App\Models\Entity\Book');

    $book = $booksRepository->find($id);

/**
 * Verifica se existe um livro com a ID informada
 */
    if (!$book) {
        throw new \Exception("Book not Found", 404);
    }

    $return = $response->withJson($book, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

/**
 * Cadastra um novo <Livro></Livro>
 * @request curl -X POST http://localhost:8000/book -H "Content-type: application/json" -d '{"name":"O Oceano no Fim do Caminho", "author":"Neil Gaiman"}'
 */
$app->post('/books', function (Request $request, Response $response) use ($app) {

    $params = (object) $request->getParams();
    /**
     * Pega o Entity Manager do nosso Container
     */
    $entityManager = $this->get('em');

    /**
     * Instância da nossa Entidade preenchida com nossos parametros do post
     */
    $book = (new Book())->setName($params->name ?? null)->setAuthor($params->author ?? null);
    

    /**
     * Verifica se existe um livro com a ID informada
     */
    if (!$book) {
        throw new \Exception("Book not Found", 404);
    }

    /**
     * Persiste a entidade no banco de dados
     */
    $entityManager->persist($book);
    $entityManager->flush();

    $logger = $this->get('logger');
    $logger->info('Book Created!', $book->getValues());

    $return = $response->withJson($book, 201)
                       ->withHeader('Content-type', 'application/json');

  

    return $return;
});

/**
 * Atualiza os dados de um livro
 * @request curl -X PUT http://localhost:8000/book/14 -H "Content-type: application/json" -d '{"name":"Deuses Americanos", "author":"Neil Gaiman"}'
 */
$app->put('/books/{id}', function (Request $request, Response $response) use ($app) {
    /**
     * Pega o ID do livro informado na URL
     */
    $route = $request->getAttribute('route');
    $id = $route->getArgument('id');
    /**
     * Encontra o Livro no Banco
     */
    $entityManager = $this->get('em');
    $booksRepository = $entityManager->getRepository('App\Models\Entity\Book');
    $book = $booksRepository->find($id);

    /**
     * Verifica se existe um livro com a ID informada
     */
    if (!$book) {
        throw new \Exception("Book not Found", 404);
    }

    /**
     * Atualiza e Persiste o Livro com os parâmetros recebidos no request
     */
    $book->setName($request->getParam('name'))
        ->setAuthor($request->getParam('author'));
    /**
     * Persiste a entidade no banco de dados
     */
    $entityManager->persist($book);
    $entityManager->flush();

    $return = $response->withJson($book, 200)
        ->withHeader('Content-type', 'application/json');
    return $return;
});

/**
 * Deleta o livro informado pelo ID
 * @request curl -X DELETE http://localhost:8000/book/3
 */
$app->delete('/books/{id}', function (Request $request, Response $response) use ($app) {
    /**
     * Pega o ID do livro informado na URL
     */
    $route = $request->getAttribute('route');
    $id = $route->getArgument('id');
    /**
     * Encontra o Livro no Banco
     */
    $entityManager = $this->get('em');
    $booksRepository = $entityManager->getRepository('App\Models\Entity\Book');
    $book = $booksRepository->find($id);
    /**
     * Remove a entidade
     */
    $entityManager->remove($book);
    $entityManager->flush();
    $return = $response->withJson(['msg' => "Deletando o livro {$id}"], 204)
        ->withHeader('Content-type', 'application/json');
    return $return;
});
$app->run();

<?php

namespace Tasks;

use Doctrine\ORM\Tools\Pagination\Paginator;
use \Zend\Diactoros\Response;
use \Zend\Diactoros\ServerRequest;
use \Doctrine\ORM\EntityManager;
use \Tasks\Entity\Task;
class Controller
{
    public function listAction(ServerRequest $request, EntityManager $em)
    {
        $pageId = $request->getAttribute("pid");
        if ($pageId == null) {
            $pageId = 1;
        }
        $sortId = $request->getAttribute("sid");
        if ($sortId == null) {
            $sortId = 'id_ASC';
        }
        $sort = explode("_",$sortId);
        $qb = $em->createQueryBuilder();
        $qb->select('count(t.id)');
        $qb->from('\Tasks\Entity\Task','t');
        $count = (int)$qb->getQuery()->getSingleScalarResult();
        $pcount = (int)ceil($count/3);
        $repository = $em->getRepository("\Tasks\Entity\Task");
        $qb = $repository->createQueryBuilder("t")
        ->orderBy('t.'.$sort[0],$sort[1])
        ->setFirstResult(($pageId-1)*3)
        ->setMaxResults(3);
        $tasks = $qb->getQuery()->execute();
        $response = new Response();
        /*foreach ($tasks as $task){
            $response->getBody()->write("id: ".$task->getId()." mail ". $task->getMail());
        }*/
        $loader = new \Twig\Loader\FilesystemLoader('templates');
        $twig = new \Twig\Environment($loader);

        $response->getBody()->write($twig->render('index.html',
            [
                'tasks' => $tasks,
                'auth'=> $this->authorized($request),
                'pages' => $pcount,
                'active' => $pageId,
                'sort' => $sortId,
            ]));
        return $response;

    }
    public function saveAction(ServerRequest $request, EntityManager $em)
    {
        if ($this->authorized($request)) {
            $taskId = (int)$request->getAttribute("id");
            $post = $request->getParsedBody();
            $task = $em->getRepository(Task::class)->find($taskId);
            if ($task->getText()!=$post["text"]) $task->setAdmined(1);
            $task->setText($post["text"]);
            $em->flush();
            $response = new Response();
            $response->getBody()->write("ok");
            return $response;
        } else
        {
            $response = new Response();
            $response->getBody()->write("auth error");
            return $response;
        }
    }

    public function newAction(ServerRequest $request, EntityManager $em)
    {
        $post = $request->getParsedBody();
        $task = new Task();
        $task->setName($post["name"]);
        $task->setMail($post["email"]);
        $task->setText($post["text"]);
        $task->setAdmined(0);
        $task->setComplited(0);
        $em->persist($task);
        $em->flush();
        $response = new Response();
        $response->getBody()->write("ok");
        return $response;
    }

    public function deleteAction(ServerRequest $request, EntityManager $em)
    {
        if ($this->authorized($request)) {
            $taskId = (int)$request->getAttribute("id");
            $task = $em->getRepository(Task::class)->find($taskId);
            $em->remove($task);
            $em->flush();
            $response = new Response();
            $response->getBody()->write("ok");
            return $response;
        } else
        {
            $response = new Response();
            $response->getBody()->write("auth error");
            return $response;
        }
    }
    public function authAction(ServerRequest $request, EntityManager $em)
    {
        $response = new Response();
        $loader = new \Twig\Loader\FilesystemLoader('templates');
        $twig = new \Twig\Environment($loader);

        $response->getBody()->write($twig->render('index3.html', []));
        return $response;

    }
    public function authPostAction(ServerRequest $request, EntityManager $em)
    {
        $post = $request->getParsedBody();
        $response = new Response();
        if ($post["login"]=="admin"&&$post["password"]=="123"){
            setcookie('taskuser', 'admin');
            setcookie('taskpass', md5("123"));
            $response = new Response();
            $response->getBody()->write("<html><script>location.href=\"/\"</script></html>");
            return $response;

        } else
        {
            $response = new Response();
            $response->getBody()->write("<html><script>alert(\"login or password not valid\");location.href=\"/auth\"</script></html>");
            return $response;

        }

    }
    private function authorized(ServerRequest $request)
    {
        if($request->getCookieParams()["taskuser"]=="admin"&&
            $request->getCookieParams()["taskpass"]==md5("123")){
            return true;
        } else

        return false;
    }
    public function logoutAction(ServerRequest $request, EntityManager $em)
    {
        setcookie('taskuser', '');
        setcookie('taskpass',"");
        $response = new Response();
        $response->getBody()->write("<html><script>location.href=\"/\"</script></html>");
        return $response;

    }
    public function completeAction(ServerRequest $request, EntityManager $em)
    {
        if ($this->authorized($request)) {
            $taskId = $request->getAttribute("id");
            $task = $em->getRepository(Task::class)->find($taskId);
            $task->setComplited(1);
            $em->flush();
            $response = new Response();
            $response->getBody()->write("ok");
            return $response;
        } else
        {
            $response = new Response();
            $response->getBody()->write("auth error");
            return $response;
        }
    }

}
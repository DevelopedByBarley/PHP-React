<?php
use App\Controllers\AdminController;
use App\Controllers\AdminRender;

// route_group -> /admin

// Operations

$r->addRoute('POST', '/login', [AdminController::class, 'login']);
$r->addRoute('POST', '/logout', [AdminController::class, 'logout']);
$r->addRoute('POST', '/store', [AdminController::class, 'store']);
$r->addRoute('POST', '/update', [AdminController::class, 'update']);
$r->addRoute('POST', '/delete/{id}', [AdminController::class, 'delete']);
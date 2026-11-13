<?php

namespace Tests\Feature;

use Tests\TestCase;

class ChatbotTest extends TestCase
{
    public function test_interfaz_del_asistente_se_sirve_desde_laravel(): void
    {
        $this->get(route('chatbot.index'))
            ->assertOk()
            ->assertHeader('content-type', 'text/html; charset=UTF-8');
    }

    public function test_disparadores_del_asistente_aparecen_en_paginas_publicas(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('data-chatbot-open')
            ->assertSee('¿Consultas? Habla con la IA');

        $this->get('/ayuda')
            ->assertOk()
            ->assertSee('Consulta al asistente IA')
            ->assertSee('data-chatbot-open');
    }
}

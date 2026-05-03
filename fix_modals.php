<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

$oldElect = <<<'HTML'
                <div class="modal-body py-4">
                    <form action="{{ route('battles.action.elect_marshall', $battle) }}" method="POST" id="electMarshallForm">
                    <div class="mb-3 position-relative">
                        @csrf
HTML;

$newElect = <<<'HTML'
                <form action="{{ route('battles.action.elect_marshall', $battle) }}" method="POST" id="electMarshallForm">
                <div class="modal-body py-4">
                    <div class="mb-3 position-relative">
                        @csrf
HTML;

$content = str_replace($oldElect, $newElect, $content);

$oldInvite = <<<'HTML'
                <div class="modal-body py-4">
                    <form action="{{ route('battles.action.invite', $battle) }}" method="POST" id="invitePlayerForm">
                    <div class="mb-3 position-relative">
                        @csrf
HTML;

$newInvite = <<<'HTML'
                <form action="{{ route('battles.action.invite', $battle) }}" method="POST" id="invitePlayerForm">
                <div class="modal-body py-4">
                    <div class="mb-3 position-relative">
                        @csrf
HTML;

$content = str_replace($oldInvite, $newInvite, $content);

file_put_contents('resources/views/battles/room.blade.php', $content);

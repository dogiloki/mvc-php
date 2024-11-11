@extends('layouts.head')

{{style('form.css')}}

<div class="form-container">
    <form class="form">

        <div class="form-title">REGISTRO DE MENSAJE</div>

        <div class="input-container">
            <label class="input-label">No. Documento</label>
            <input class="input-field" type="text">
        </div>
        <div class="input-container">
            <label class="input-label">Referencia</label>
            <input class="input-field" type="text">
        </div>
        <div class="input-container">
            <label class="input-label">Remitente</label>
            <input class="input-field" type="text">
        </div>

        <div class="button-container">
            <a href="" class="button button-secondary">Cancelar</a>
            <button class="button button-primary">Agregar</button>
        </div>

    </form>
</div>

{{style('table.css')}}

<div class="table-container">

    <div class="pagination-container">
        <div class="pagination-buttons">
            <a class="button" href=""><</a>
            <a class="button button-active" href="">1</a>
            <a class="button" href="">2</a>
            <a class="button" href="">3</a>
            <a class="button" href="">4</a>
            <a class="button" href="">5</a>
            <a class="button" href="">></a>
        </div>
        <div class="pagination-info">
            <div class="current-page">1</div>/
            <div class="total-pages">10 pág.</div>
            <div class="total-results">100 resultado</div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>No. Documento</th>
                <th>Referenicia</th>
                <th>Remitente</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>945</td>
                <td>Parte de Novedades</td>
                <td>6/o. BTN. GN.</td>
            </tr>
            <tr>
                <td>945</td>
                <td>Parte de Novedades</td>
                <td>6/o. BTN. GN.</td>
            </tr>
            <tr>
                <td>945</td>
                <td>Parte de Novedades</td>
                <td>6/o. BTN. GN.</td>
            </tr>
            <tr>
                <td>945</td>
                <td>Parte de Novedades</td>
                <td>6/o. BTN. GN.</td>
            </tr>
            <tr>
                <td>945</td>
                <td>Parte de Novedades</td>
                <td>6/o. BTN. GN.</td>
            </tr>
        </tbody>
    </table>

</div>

@extends('layouts.footer')
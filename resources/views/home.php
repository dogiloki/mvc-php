@extends('layouts.head')

{{style('form.css')}}

<div class="form-container">
    <form class="form2">

        <div class="form-title">REGISTRO DE MENSAJE</div>

        <div class="input-container">
            <input class="input-field" type="text" placeholder="" required>
            <label class="input-label">No. Documento</label>
        </div>
        <div class="input-container">
            <input class="input-field" type="text" placeholder="" required>
            <label class="input-label">Referencia</label>
        </div>
        <div class="input-container">
            <input class="input-field" type="text" placeholder="" required>
            <label class="input-label">Remitente</label>
        </div>

        <div class="button-container">
            <a href="" class="button button-secondary">Cancelar</a>
            <button class="button button-primary">Agregar</button>
        </div>

    </form>
</div>

{{style('table.css')}}

<!--

<section is="data-table" class="table-container">

    <div class="pagination-container"></div>
    <table model-table="User" class="table">
        <thead>
            <tr>
                <th data-column="name">Nombre</th>
                <th data-column="email">Email</th>
                <th data-column="full()">Método</th>
            </tr>
        </thead>
    </table>

</section>

-->

@extends('layouts.footer')
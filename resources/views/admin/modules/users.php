<link rel="stylesheet" href="{{scss('modules/users.scss')}}">
<h2>Usuarios</h2>
<div class="container-table">
    <div class="container-paginator">
        <div class="container-pages" id="container-pages"></div>
        <div class="container-total-pages">
            <div id="current-page"></div>/
            <div id="total-pages"></div> pag.
            <div id="total-results"></div>resultados
        </div>
    </div>
    <table id="table-users">
        <thead>
            <tr>
                <th>GRADO</th>
                <th>EMPLEO</th>
                <th>NOMBRE(S)</th>
                <th>APELLIDOS</th>
                <th>USUARIO</th>
                <th>PERMISOS</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script type="module" src="{{url('js/modules/users.js')}}"></script>
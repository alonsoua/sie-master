<script>
 function fn_agregar(){
    cadena=[];
    cadena = "<tr>";
    cadena = cadena + "<input type='checkbox' name=dia[]";
    cadena = cadena + "<td>";
}
</script>

<!--creo una tabla para mostrar los datos en forma de libro-->
<table>
    <tr>
        <th>Rut</th>
        <th>Nombre</th>
        <th>APaterno</th>
        <th>Amaterno</th>
        <th>1</th>
        <th>2</th>
        <th>3</th>
        <th>4</th>
        <th>5</th>
        <th>6</th>
        <th></th>
        <th>7</th>
        <th>8</th>
        <th>9</th>
        <th>10</th>
        <th>11</th>
        <th>12</th>
        <th>12</th>
        <th>13</th>
        <th>14</th>
        <th>15</th>
        <th>16</th>
        <th>17</th>
        <th>18</th>
        <th>19</th>
        <th>20</th>
        <th>21</th>
        <th>22</th>
        <th>23</th>
        <th>24</th>
        <th>25</th>
        <th>26</th>
        <th>27</th>
        <th>28</th>
        <th>29</th>
        <th>30</th>
        <th>31</th>
        
    </tr>

<!--    recorro el arreglo de datos-->
    <?php foreach ($this->dato as $d) : ?>
<!--           por cada fila, muestro sus datos -->

        <tr>
<!--            nombre del Establecimiento, con un enlace para editar el Establecimiento-->
            <td>
                
                   <?php echo $d->idlibro_clases; ?>
            
        </td>

<!--                   nombre Curso-->
        <td><?php echo $d->nombre_curso; ?></td>
        <td><?php echo $d->nombre_curso; ?></td>
        
        
         <td>
             <!--                   enlace para editar el libro-->
             <a class="button small green" href="<?php echo $this->url(array('controller' => 'Libro',
            'action' => 'editar', 'idLibro' => $d->idlibro_clases)); ?>">Editar</a>
             
         </td>
         fn_agregar();
        </tr>
<!--    fin del for-->
    <?php endforeach; ?>
</table>

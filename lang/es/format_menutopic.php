<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'format_menutopic', language 'es'
 *
 * @since 2.3
 * @package format_menutopic
 * @copyright 2012 David Herney Bernal - cirano
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['aboutresource'] = 'Acerca del recurso';
$string['aboutsection'] = 'Acerca del tema';
$string['actionadd_sheet_daughter_sheetedit'] = 'Adicionar como hija';
$string['actionadd_sheet_sister_sheetedit'] = 'Adicionar como hermana';
$string['actiondelete_sheet_sheetedit'] = 'Eliminar';
$string['actiondeleteconfirm_sheet_sheetedit'] = 'Si elimina la hoja eliminará todas las hojas hijo ¿Seguro que desea continuar?';
$string['actiondown_sheet_sheetedit'] = 'Mover abajo';
$string['actionleft_sheet_sheetedit'] = 'Mover a la izquierda';
$string['actionright_sheet_sheetedit'] = 'Mover a la derecha';
$string['actions_sheet_sheetedit'] = 'Acciones sobre la hoja';
$string['actionsave_sheet_sheetedit'] = 'Cambiar datos de la hoja';
$string['actionup_sheet_sheetedit'] = 'Mover arriba';
$string['config_editmenu'] = 'Configurar';
$string['config_editmenu_title'] = 'Configuraciones de menú';
$string['config_template_topic_title'] = 'Configuraciones de funcionalidad -Descripción de cada sección como plantilla-';
$string['coursedisplay'] = 'Modo de visualización de la sección 0';
$string['coursedisplay_help'] = 'Define como se muestra la sección 0: como un elemento del menú o como una sección encima del menú.';
$string['coursedisplay_multi'] = 'Arriba del menú';
$string['coursedisplay_single'] = 'Como elemento del menú';
$string['csscode'] = 'Código CSS';
$string['cssdefault'] = 'Incluir estilos CSS por defecto';
$string['cssdefault_help'] = '<p>Define si se incluyen los estilos CSS por defecto para el menú. Sólo aplica cuando no se incluye el Javascript por defecto ya que de lo contrario siempre se incluyen los estilos</p>
<p>Puede ser útil deshabilitar esta opción para incluir estilos personalizados mediante la opción <b>"Plantilla de estilos (CSS)"</b></p>';
$string['csstemplate'] = 'Estilo CSS';
$string['csstemplate_editmenu'] = 'Estilos (CSS)';
$string['csstemplate_editmenu_title'] = 'Estilos CSS';
$string['csstemplate_help'] = '<p>Permite incluir estilos CSS personalizados con lo cual se puede definir una apariencia gráfica personalizada para el menú.</p>
<p>Un ejemplo sencillo de utilización de la plantilla de estilos sería:</p>
<div style=" white-space:nowrap; font-size: 12px; border: 1px solid #666; padding: 5px; background-color: #CCC">
#format_menutopic_menu { margin-bottom: 10px; }
</div>
<p>Con el anterior código se separa 10px el contenido que esté por debajo del menú, según la posición definida para el menú.</p>
<p><strong>Nota:</strong>
<ul>
    <li>El identificador (id) de la capa (div) que contiene el menú es <strong>format_menutopic_menu</strong>. Este dato puede ser útil para manipular los estilos del menú sin afectar otros componentes de la página.</li>
    <li>Al realizar cambios en los estilos es probable que los cambios no se vean inmediatamente en el curso, de ser así, se deberá refrescar la página. En muchos navegadores se puede realizar este refresco presionando Ctrl+F5.</li>
</ul></p>';
$string['currentsection'] = 'Este tema';
$string['defaultsectionsnavigation'] = 'Valor predeterminado para la navegación por secciones';
$string['defaultsectionsnavigation_help'] = 'Valor predeterminado que se utiliza en los cursos para definir la característica "Usar navegación por secciones". Se puede sobrescribir para cada curso.';
$string['displaynavigation'] = 'Mostrar navegación';
$string['displaynavigation_help'] = 'Indica si se desea mostrar la navegación entre secciones y la posición donde se mostraría.';
$string['displaynousedmod'] = 'Mostrar recursos no incluidos en plantilla';
$string['displaynousedmod_help'] = 'Not implemented yet';
$string['editmenu'] = 'Editar menú';
$string['enableanchorposition'] = 'Habilitar posición de ancla';
$string['enableanchorposition_help'] = 'Utilice un ancla para navegar a la parte superior del menú cuando haga clic en una opción del menú.';
$string['end_editmenu'] = 'Finalizar Editar menú';
$string['error_jsontree'] = 'Error en la estructura de datos retornada como composición del árbol';
$string['globalstyle'] = 'Estilo global';
$string['globalstyle_help'] = 'Esto define el estilo del menú.';
$string['hidden_message'] = 'El tema <em>{$a}</em> no está disponible en este momento.';
$string['hiddenmenubar'] = 'El menú está configurado para que esté oculto. No se verá cuando no se esté en modo de edición.';
$string['hidefromothers'] = 'Ocultar tema';
$string['htmlcode'] = 'HTML';
$string['htmltemplate_editmenu'] = 'Plantilla de html';
$string['htmltemplate_editmenu_title'] = 'Fuentes HTML';
$string['icons_templatetopic'] = 'Mostrar íconos en recursos';
$string['icons_templatetopic_help'] = 'Not implemented yet';
$string['jscode'] = 'Código';
$string['jsdefault'] = 'Incluir JavaScript por defecto';
$string['jsdefault_help'] = '<p>Define si se incluyen las funciones de JavaScript que generan el menú. En caso de que se establezca como <b>No</b>, el menú
se generará como una lista</p>
<p>
Puede ser útil deshabilitar el JavaScript por defecto si se desea dar otra apariencia al menú, como por ejemplo tipo Blog. También es posible utilizar código JavaScript que puede
ser incluido en la <b>"Plantilla de Javascript"</b>, para ello es preciso seguir las referencias de las funciones utilizadas y que corresponden al "MenuNav Node Plugin" de la librería
<a href="http://yuilibrary.com/yui/docs/node-menunav/" target="_blank">YUI3</a> que se incluye en moodle.</p>';
$string['jstemplate'] = 'Fuentes JavaScript';
$string['jstemplate_editmenu'] = 'Plantilla de javascript';
$string['jstemplate_editmenu_title'] = 'Fuentes JavaScript';
$string['jstemplate_help'] = '<p>Permite definir código JavaScript que actua sobre el menú o sobre la página. Puede servir para definir comportamientos adicionales para el menú
o incluso para definir una estructura de menú diferente a la por defecto.</p>
<p>Si se deshabilita el JavaScript por defecto, en la pestaña <strong>"Configurar"</strong>, se  puede incluir código que manipule la información del menú y construya un menú nuevo,
para ello es preciso seguir las referencias de las funciones utilizadas y que corresponden al "MenuNav Node Plugin" de la librería <a href="http://yuilibrary.com/yui/docs/node-menunav/" target="_blank">YUI3</a> que se incluye en moodle.</p>
<p><b>Notas:</b>
<ul>
    <li>El identificador (id) de la capa (div) que contiene el menú es <strong>format_menutopic_menu</strong>, allí se encuentra el menú en HTML construido como listas anidadas, normalmente con las etiquetas HTML: ul y li.</li>
    <li>Al realizar cambios en el JavaScript es probable que los cambios no se vean inmediatamente en el curso, de ser así, se deberá refrescar la página. En muchos navegadores se puede realizar este refresco presionando Ctrl+F5.</li>
</ul></p>';
$string['linkinparent'] = 'Mantener enlaces en raices de submenú';
$string['linkinparent_help'] = '<b>Solamente funciona en el menú con estilo básico porque Bootstrap no soporta esta funcionalidad.</b>
<p>Define el comportamiento de las opciones del menú que actúan como raices o padres de un submenú.</p>
<p>Si se establece en <b>Sí</b>, el ítem del menú actúa como enlace al dar clic sobre él y abre la URL que se le define en el <b>"Árbol del menú"</b>. Si se establece en <b>No</b>, el ítem del menú despliega los enlaces hijos al dar clic sobre él.</p>';
$string['menuposition'] = 'Posición del menú';
$string['menuposition_help'] = '<p>Define la posición en la cual aparecerá el menú en el curso. Las posibles opciones son:
<ul>
    <li><b>No mostrar:</b> no se genera el menú</li>
    <li><b>Izquierda:</b> el menú es generado verticalmente en la columna de bloques de la izquierda, si no hay bloque al lado izquierdo entonces se ve el menú en el centro.</li>
    <li><b>Centro:</b> el menú se genera horizontalmente como una barra en la parte central del curso</li>
    <li><b>Derecha:</b> el menú es generado verticalmente en la columna de bloques de la derecha, si no hay bloque al lado derecho entonces se ve el menú en el centro. En esta ubicación, los submenús presentan problemas de visualización debidos a la librería de JavaScript utilizadas.</li>
</ul></p>';
$string['menuposition_hide'] = 'No mostrar';
$string['menuposition_left'] = 'Izquierda';
$string['menuposition_middle'] = 'Centro';
$string['menuposition_right'] = 'Derecha';
$string['name_sheet_sheetedit'] = 'Nombre de la hoja';
$string['navbartitle'] = 'Secciones';
$string['navigationposition_both'] = 'Arriba y abajo';
$string['navigationposition_bottom'] = 'Abajo';
$string['navigationposition_nothing'] = 'No mostrar';
$string['navigationposition_site'] = 'Utilice el valor predeterminado del sitio';
$string['navigationposition_slide'] = 'Como diapositivas';
$string['navigationposition_support'] = 'Solo si el tema no admite la función "utilizar índice de cursos"';
$string['navigationposition_top'] = 'Arriba';
$string['next_topic'] = 'Siguiente';
$string['nodesnavigation'] = 'Nodos de navegación';
$string['nodesnavigation_help'] = '<p>Números de las secciones, separados por coma. Si se deja vacío, se genera la navegación automáticamente según el número de la sección y de manera consecutiva. No deben haber números de secciones repetidos porque se mostraría siempre la navegación desde la primer coincidencia encontrada.</p>
<p><b>Ejemplo correcto:</b> 1,2,8,10,3</p>';
$string['notsaved'] = 'La información no se pudo almacenar';
$string['page-course-view-topics'] = 'Alguna página principal de curso en formato menutopic';
$string['page-course-view-topics-x'] = 'Alguna página de curso en formato menutopic';
$string['plugin_description'] = 'Las secciones del curso se muestran como un menú.';
$string['pluginname'] = 'Temas desde menú';
$string['previous_topic'] = 'Anterior';
$string['privacy:metadata'] = 'El formato Temas desde menú no almacena datos personales.';
$string['savecorrect'] = 'La información se almacenó satisfactoriamente';
$string['sectionname'] = 'Tema';
$string['separator_navigation'] = ' - ';
$string['showfromothers'] = 'Mostrar tema';
$string['shownavbarbrand'] = 'Mostrar marca de la barra de navegación';
$string['shownavbarbrand_help'] = 'Mostrar el texto de la marca de la barra de navegación en los estilos Bootstrap.';
$string['style_basic'] = 'Básico';
$string['style_boots'] = 'Bootstrap';
$string['style_bootsdark'] = 'Bootstrap oscuro';
$string['target_sheet_sheetedit'] = 'Destino del enlace';
$string['targetblank_sheet_sheetedit'] = 'Nueva ventana';
$string['targetself_sheet_sheetedit'] = 'La misma ventana';
$string['template_namemenutopic'] = 'Tema {$a}';
$string['templatetopic'] = 'Activar Descripción de cada sección como plantilla';
$string['templatetopic_help'] = 'Not implemented yet';
$string['title_panel_sheetedit'] = 'Editar hoja del árbol';
$string['togglemenu'] = 'Activar/desactivar menú';
$string['topic_sheet_sheetedit'] = 'Sección destino';
$string['tree_editmenu'] = 'Árbol del menú';
$string['tree_editmenu_title'] = 'Configurar árbol de temas';
$string['tree_struct'] = 'Estructura del árbol';
$string['tree_struct_help'] = '<p>La base del menú es una estructura de árbol donde cada rama u hoja del árbol puede estar asociada a una URL. La URL puede ser externa o estár vinculada directamente a una sección del curso. Cuando se ingresa por primera vez a configurar el árbol, la plataforma sugiere una estructura lineal, sin ramas, con una cantidad de hojas igual al número de secciones del curso.</p>
<p>Al pasar el mouse sobre una hoja se ven las opciones de edición. Entre las acciones que se pueden realizar sobre la hoja están:</p>
<ul>
    <li>
        <strong>Editar una hoja (&#9997;):</strong> permite modificar todos los parámetros de la hoja actual. Las propiedades que pueden ser modificadas son:
        <ul>
            <li><strong>Nombre de la hoja:</strong> la etiqueta que aparece para esa hoja en el menú.</li>
            <li><strong>Sección destino:</strong> Si la hoja se utiliza como referencia a una sección del curso, esta opción indica cual sección será la seleccionada. Si se selecciona una sección no se podrá definir luego una URL externa a la cual dirigir el enlace de la opción en el menú.</li>
            <li><strong>URL:</strong> indica una URL a la cual hará referencia la opción del menú. Solo puede ser especificada si no se seleccionó una sección destino.</li>
            <li><strong>Destino del enlace:</strong> Indica si se desea abrir el enlace, sea de la sección o de la URL externa, en una nueva ventana o en la misma ventana. Si no se selecciona una opción, el enlace se abre en la misma ventana.</li>
        </ul>
    </li>
    <li><strong>Mover una hoja a la izquierda (&larr;):</strong> convierte a la hoja en hermana de la hoja que la contiene (hoja padre). Solo está disponible si la hoja es hija de otra hoja, nunca si se encuentra en la rama principal.</li>
    <li><strong>Mover una hoja a la derecha (&rarr;):</strong> convierte la hoja en hija de la hoja anterior. No está disponible para la primera hoja de la rama principal.</li>
    <li><strong>Subir una hoja (&uarr;):</strong> cambia el orden de una hoja colocandola antes de su hermano inmediatamente anterior. No está disponible para la primera hoja de una rama.</li>
    <li><strong>Bajar una hoja (&darr;):</strong> cambia el orden de una hoja colocandola despues de su hermano inmediatamente posterior. No está disponible para la última hoja de una rama.</li>
    <li><strong>Eliminar una hoja (&#10008;):</strong> elimina la hoja seleccionada y todas las hojas que contiene.</li>
    <li><strong>Adicionar una hoja (&#10010;):</strong> adiciona una nueva hoja como hija de la actual.</li>
</ul>
<p>Los cambios hechos en el menú solo son almacenados al seleccionar la opción <strong>&quot;Guardar cambios&quot;</strong> en la parte inferior de la página.</p>';
$string['url_sheet_sheetedit'] = 'URL';
$string['usescourseindex'] = 'Utiliza el índice del curso';
$string['usescourseindex_help'] = 'Utiliza la barra de <em>índice del curso</em> para navegar por las secciones y los recursos';
$string['usessectionsnavigation'] = 'Utiliza la navegación por secciones';
$string['usessectionsnavigation_help'] = 'Mostrar botones para navegar a la sección siguiente o anterior.';

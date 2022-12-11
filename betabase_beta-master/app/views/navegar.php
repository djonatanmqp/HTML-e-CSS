<?php 

// Busca o nome de um organismo em um arquivo PDB
function buscaOrganismo($pdbid){
	$pdbid = strtolower($pdbid);
	$pdb = fopen("app/data/pdb/$pdbid.pdb","r");
	
	while(!feof($pdb)){
		$linha = fgets($pdb, 4096);
		$tipo = substr($linha, 11, 19);
		if($tipo === 'ORGANISM_SCIENTIFIC'){
			$organismo = substr($linha, 32);
			$organismo = str_replace(';','', $organismo);
		}
		if(isset($organismo)) break;
	}

	fclose ($pdb);
	return ucfirst(strtolower($organismo));

}
?>

<script type="text/javascript">

var glviewer = null;
var labels = [];


var colorSS = function(viewer) {

		//color by secondary structure
		var m = viewer.getModel();
		viewer.setStyle({},{cartoon:{}}); /* Cartoon */

		m.setColorByFunction({}, function(atom) {
			if(atom.ss == 'h') return "magenta";
			else if(atom.ss == 's') return "orange";
			else return "white";
		});
		viewer.render();
}


var atomcallback = function(atom, viewer) {
		if (atom.clickLabel === undefined
				|| !atom.clickLabel instanceof $3Dmol.Label) {
			atom.clickLabel = viewer.addLabel(atom.resn + " " + atom.resi + " ("+ atom.elem + ")", {
				fontSize : 10,
				position : {
					x : atom.x,
					y : atom.y,
					z : atom.z
				},
				backgroundColor: "black"
			});
			atom.clicked = true;
		}

		//toggle label style
		else {

			if (atom.clicked) {
				var newstyle = atom.clickLabel.getStyle();
				newstyle.backgroundColor = 0x66ccff;

				viewer.setLabelStyle(atom.clickLabel, newstyle);
				atom.clicked = !atom.clicked;
			}
			else {
				viewer.removeLabel(atom.clickLabel);
				delete atom.clickLabel;
				atom.clicked = false;
			}

		}
};

/* Reading PDB */
function readPDB(id){
	var txt = "app/data/pdb/"+id+".pdb";
	
	$.post(txt, function(d) {

		moldata = data = d;

		/* Creating visualization */
		glviewer = $3Dmol.createViewer("pdb_3d", {
			defaultcolors : $3Dmol.rasmolElementColors
		});

		/* Color background */
		glviewer.setBackgroundColor(0xffffff);

		receptorModel = m = glviewer.addModel(data, "pqr");

		/* Type of visualization */
		glviewer.setStyle({},{cartoon:{color:'spectrum'}}); /* Cartoon multi-color */
		glviewer.addSurface($3Dmol.SurfaceType, {opacity:0.1});   

		/* Name of the atoms */
		atoms = m.selectedAtoms({});
		for ( var i in atoms) {
			var atom = atoms[i];
			atom.clickable = true;
			atom.callback = atomcallback;
		}

		glviewer.mapAtomProperties($3Dmol.applyPartialCharges);
		glviewer.zoomTo();
		glviewer.render();
	});
}

/* Select ID */
function selectID(glviewer){
	var resID = $('#sID').val();
	glviewer.setStyle({resi:resID},{stick:{colorscheme:'whiteCarbon'}}); 
	glviewer.render();
}


</script>

<!-- MODAL 1 -->
<div class="modal fade" id="pdb_modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
   				<h4 class="modal-title" id="myModalLabel">Estrutura tridimensional</h4>
			</div>
   			<div class="modal-body">
				<div id="pdb_3d" style="width: 800px; height: 400px; margin: 0; padding: 0; border: 0;"></div>
			</div>
			<div class="modal-footer">
				<div class="form-inline">
	       			<button class="btn btn-default" onclick="colorSS(glviewer);">Colorir estrutura secundária</button>
					<div class="input-group">
						<input type="text" placeholder="Buscar resíduo por ID" id="sID" class="form-control" onform="">
							<span class="input-group-btn">
						<button class="btn btn-default" type="button" onclick="selectID(glviewer)">Exibir</button>
						</span>
					</div>
	       		</div>
   			</div>
		</div>
	</div>
</div>
<!-- FIM MODAL 1 -->

<!-- Primeira parte -->
<div id="main" class="center">
	<span style="color:#9bbd46">- NAVEGAR -</span>
	<h2>Explore nossa base de dados</h2>
</div>
<div id="navegar">
	<div class="container">

		<!-- Criando a tabela -->
		<table class="table table-condensed table-hover table-striped">
			<thead>
				<tr>
					<th>#</th>
					<th>PDB ID</th>
					<th>Show PDB</th>
					<th>Organismo de origem</th>					
					<th>Sequências</th>
				</tr>
			</thead>
			<tbody>

<?php 
// Abrir arquivo de sequencias 
$arquivo = file_get_contents("app/data/seq.fasta"); 
$fastas = explode(">",$arquivo); // particiona o array em fastas separados
array_shift($fastas); // remove primeiro elemento
$total_fastas = count($fastas);
$pdbid_anterior = "Nenhum";
$id = 0;

for($i = 0; $i < $total_fastas; $i++){
	$pdbid = substr($fastas[$i], 0, 4);
	$cadeia = substr($fastas[$i], 5, 1);
	$seq = substr($fastas[$i], 27);
	
	if($pdbid_anterior != $pdbid){

		$pdbid_anterior = $pdbid;
		$id++; // Soma #

		if ($id != 1){ 
			echo '</td></tr><tr>'; //Abre uma nova linha
		}
		else {
			echo '<tr>'; //Abre uma nova linha
		}

		echo '<td>'.$id.'</td>'; // Grava #
		echo '<td><a href="app/data/pdb/'.strtolower($pdbid).'.pdb">'.$pdbid.'</a></td>'; // Grava pdbid
		echo '<td><a data-toggle="modal" onclick="readPDB(\''.strtolower($pdbid).'\');" data-target="#pdb_modal"><span class="glyphicon glyphicon-picture" aria-hidden="true"></span></a>';
		echo '<td><i>'.buscaOrganismo($pdbid).'</i></td>'; // Busca nome do organismo
		echo '<td><button data-toggle="modal" data-target="#'.$pdbid.'_'.$cadeia.'" style="margin-left:5px" type="button" class="btn btn-default btn-xs" title="'.$pdbid.':'.$cadeia.'" 
		>'.$cadeia.'</button>';


		// MODAL 2
		echo '<div class="modal fade" id="'.$pdbid.'_'.$cadeia.'" tabindex="-1" role="dialog">
  			<div class="modal-dialog modal-lg" role="document">
    			<div class="modal-content">
      				<div class="modal-header">
       					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
       						<span aria-hidden="true">&times;</span>
       					</button>
        				<h4 class="modal-title" id="myModalLabel">'.$pdbid.':'.$cadeia.'</h4>
      				</div>
	      			<div class="modal-body">
						<pre>'.$seq.'</pre>
					</div>
					<div class="modal-footer">
	        			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        
	      			</div>
    			</div>
			</div>
		</div>';
		// FIM MODAL 2
	}
	else{
		echo '<button type="button" data-toggle="modal" data-target="#'.$pdbid.'_'.$cadeia.'" style="margin-left:5px" class="btn btn-default btn-xs" title="'.$pdbid.':'.$cadeia.'" 
		data-container="body" data-toggle="popover" data-placement="left" 
		data-content="'.$seq.'">'.$cadeia.'</button>';

		// MODAL 3
		echo '<div class="modal fade" id="'.$pdbid.'_'.$cadeia.'" tabindex="-1" role="dialog">
  			<div class="modal-dialog modal-lg" role="document">
    			<div class="modal-content">
      				<div class="modal-header">
       					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
       						<span aria-hidden="true">&times;</span>
       					</button>
        				<h4 class="modal-title" id="myModalLabel">'.$pdbid.':'.$cadeia.'</h4>
      				</div>
	      			<div class="modal-body">
						<pre>'.$seq.'</pre>
					</div>
					<div class="modal-footer">
	        			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        
	      			</div>
    			</div>
			</div>
		</div>';
		// FIM MODAL 3
	}
}

?>

			</tbody>
		</table>
	</div>
</div>
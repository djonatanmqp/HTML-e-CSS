<?php 

error_reporting(E_ERROR | E_PARSE);

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

<!-- Primeira parte -->
<div id="main" class="center">
	<span style="color:#9bbd46">- BLAST -</span>
	<h2>Realize buscas usando a ferramenta BLAST</h2>
</div>

<!-- Segunda parte -->
<div id="blast">
	<div class="container">
		<form method="post" action="?i=blast&results">
			<!-- Tipo de blast -->
			<div class="btn-group" data-toggle="buttons">
				<label class="btn btn-default active">
					<input type="radio" name="tipo" value="p" autocomplete="off" title="BLAST PROTEIN -> PROTEIN" checked> blastp
				</label>
			</div>
			
			<textarea name="query" class="form-control" placeholder="Insira a sequência aqui" rows="5"></textarea>
			<p class="center">
				<br>
				<input type="submit"  name="submit" value="Executar BLAST" class="btn btn-primary btn-lg">
			</p>


	
			<?php // Processamento em PHP
			if(isset($_POST['submit'])){ ?>

			<!-- Crie a tabela aqui -->
			<table class="table table-condensed table-striped table-hover">
				<tr>
					<th>Identidade</th>
					<th style="text-align:center">Sequência query</th>
					<th>PDB Referência</th>
					<th>Organismo referência</th>
					<th>Identidade %</th>
					<th>Positivos %</th>
					<th>Gaps %</th>
					<th style="text-align:center">Alinhamento</th>
				</tr>

			<?php

				$query = addslashes($_POST['query']);
				$tipo = addslashes($_POST['tipo']);
				if($tipo == 'p') {
					$programa = "blastp";
				}

				$tmp = fopen("app/data/tmp.fasta","w");
				fwrite($tmp, ">Query\n".$query);
				fclose($tmp);

				//Executa blast
				system("cd app/data && ../bin/$programa -query tmp.fasta -subject seq.fasta > results.txt");
				echo "<h1>Resultados ($programa)</h1>";
				$arquivo = file_get_contents("app/data/results.txt");


				$resultados = explode("> ",$arquivo); //alterado para linux
				array_shift($resultados); //remove primeiro elemento
				?>

				<!-- MODAL 1: query -->
				<div class="modal fade" id="query" tabindex="-1" role="dialog">
					<div class="modal-dialog modal-lg" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
				   				<h4 class="modal-title" id="myModalLabel">Sequência buscada (Query)</h4>
							</div>
				   			<div class="modal-body">
								<pre><?php echo $query; ?></pre>
							</div>
							<div class="modal-footer">
					  			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				  			</div>
				  		</div>
					</div>
				</div>
				<!-- FIM MODAL -->

				<?php

				foreach($resultados as $resultado){

					// Coletando dados usando apenas comando explode

					
					$aux2 = explode("|",$resultado);
					$aux3 = explode(":",$aux2[0]);
					$pdb_id = $aux3[0];
					$cadeia = $aux3[1]; 


					$aux4 = explode("Identities = ",$resultado);
					$aux5 = explode(" (",$aux4[1]);
					$identidade = $aux5[0];


					$aux6 = explode("), Positives = ",$aux5[1]);
					$positivos = $aux6[1];

					$aux7 = explode("), Gaps = ",$aux5[2]);
					$gaps = $aux7[1];
					$alinhamento = $resultado;

					$identidade = explode("/",$identidade);
					$positivos = explode("/",$positivos);
					$gaps = explode("/",$gaps);

					$porcentagem_identidade = (int)(100*$identidade[0]/$identidade[1]);
					$porcentagem_positivos = (int)(100*$positivos[0]/$positivos[1]);
					$porcentagem_gaps = (int)(100*$gaps[0]/$gaps[1]);

					if($porcentagem_identidade != 0){

						// $pdb_id
						// $cadeia
						// $identidade[0] / $identidade[1]
						// $positivos[0] / $positivos[1]
						// $gaps[0] / $gaps[1]
						// $alinhamento

						// Iniciando os prints
						echo "<tr>";

						//<th>Região coincidente</th>
						echo '<td><div class="progress"><div class="progress-bar progress-bar-success" role="progressbar" style="min-width:'.$porcentagem_identidade.'% ;"></div></div></td>';
						
						//<th>Sequência</th>					
						echo '<td style="text-align:center"><a data-toggle="modal" data-target="#query" style="margin-left:5px" type="button" title="Query" 
						><span class="glyphicon glyphicon-align-justify" aria-hidden="true"></span></a>';

						//<th>PDB Referência</th>
						echo '<td><a title="Download PDB" alt="download pdb" href="app/data/pdb/'.strtolower($pdb_id).'.pdb">'.$pdb_id.':'.$cadeia.'</a></td>';

						//<th>Organismo referência</th>
						echo '<td><i>'.buscaOrganismo(strtolower($pdb_id)).'</i></td>';

						//<th>Identidade</th>
						echo '<td>'.$identidade[0].'/'.$identidade[1].' ('.$porcentagem_identidade.'%)</td>';

						//<th>Positivos</th>
						echo '<td>'.$positivos[0].'/'.$positivos[1].' ('.$porcentagem_positivos.'%)</td>';

						//<th>Gaps</th>
						echo '<td>'.$gaps[0].'/'.$gaps[1].' ('.$porcentagem_gaps.'%)</td>';

						//<th>Alinhamento</th>
						echo '<td style="text-align:center"><a data-toggle="modal" data-target="#alinhamento_'.$pdb_id.'_'.$cadeia.'" style="margin-left:5px" type="button" title="'.$pdbid.':'.$cadeia.'" 
						><span class="glyphicon glyphicon-search" aria-hidden="true"></span></a>';

						echo '</tr>';

						////////////////////////////////////////////////////////////////////////////////////
						// MODAL 2: alinhamento
						echo '<div class="modal fade" id="alinhamento_'.$pdb_id.'_'.$cadeia.'" tabindex="-1" role="dialog">
				  			<div class="modal-dialog modal-lg" role="document">
				    			<div class="modal-content">
				      				<div class="modal-header">
				       					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				       						<span aria-hidden="true">&times;</span>
				       					</button>
				      				</div>
					      			<div class="modal-body">
										<pre>'.$alinhamento.'</pre>
									</div>
									<div class="modal-footer">
					        			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					        
					      			</div>
				    			</div>
							</div>
						</div>';
						// FIM MODAL 2
						///////////////////////////////////////////////////////////////////////////////////////////
					}
				}
				echo '</table>';
			}
			?>
</div>
</div>
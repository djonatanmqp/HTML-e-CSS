<!-- MODAL HELP -->
<div class="modal fade" id="help" tabindex="-1" role="dialog" aria-labelledby="help">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Ajuda</h4>
      </div>
      <div class="modal-body">
        <p style="text-align:center"><img src="app/img/logo.svg" alt="logo betabase"></p><br>
        <p>Betabase é uma base de dados com todas as sequências e estruturas de beta-glicosidases
        	disponíveis no PDB.</p>
        <p>Pressione <b>navegar</b> para obter uma lista de beta-glicosidases disponíveis. Para buscar
        	novas sequências, clique no menu <b>BLAST</b>. Para mais informações, entre em contato com 
        	<a href="#">Prof. Djonatan Piehowiak</a>.
        </p><br>
        <p><label class="label label-default">Atualizado em:</label> 12 de dezembro, 2022</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>
<!-- FIM Modal -->


<footer>
	<div class="container">
		<div class="row">
			<div class="col-md-2"><a href="#"><img id="logo" src="app/img/logo.svg"></a></div>
			<div class="col-md-1 lista_footer"><a href="?i=sobre">Sobre</a></div>
			<div class="col-md-1 lista_footer"><a href="?i=navegar">Navegar</a></div>
			<div class="col-md-1 lista_footer"><a href="?i=blast">Buscar</a></div>
			<div class="col-md-1 lista_footer"><a href="#" data-toggle="modal" data-target="#help">Ajuda</a></div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<p>© 2017 betabase | Todos os direitos reservados. Construido por <a href="#">Diego Mariano</a>.</p>
			</div>
		</div>
	</div>
	<!-- scripts -->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="app/js/bootstrap.min.js"></script>
    <script src="app/js/3Dmol-min.js"></script>
  <!-- fim scripts -->
</footer>
</body>
</html>
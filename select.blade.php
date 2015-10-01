<h2>Search for an Employee</h2>
<ul class="nav nav-tabs">
  <li class="active" id='tab-quick'><a     href='#pane-quick'data-toggle='tab'>Quick</a></li>
  <li><a href='#pane-advanced' id='tab-advanced' data-toggle='tab'>Advanced</a></li>
</ul>

<div class="well">
  <div class='tab-content'>
    <div class="tab-pane active" id='pane-quick'>
      <div class="container">
        <div class="form-horizontal">
          {{ Form::label('namesearch', 'Enter part or all of a forename, surname or employers ID.', ['class' => 'control-label col-sm-10']) }}
          <div class="col-sm-10">
              <input name="namesearch" id="namesearch" class="form-control" placeholder="Enter a name" size="45">
          </div>
        </div>
      </div> <!-- container -->
  </div> <!-- quick -->


  <div class="tab-pane short-label" id='pane-advanced'>
   <!--    <form action="/orchid/jax/employees/advSearch" method='POST'> -->

    <form id = 'fullSearch' class='advsrch'   method='post'>
      <ul>
        <li><label for='location'>Location: </label><input type='select' name='location' length=25></li>
        <li><label for='forename'>Forename: </label><input type='select' name='forename' length=25></li>
        <li><label for='surname'>Surname: </label><input type='select' name='surname'  length=25></li>
        <li><label for='address'>Address: </label><input type='select' name='address' length=25></li>
        <li><label for='town'>Town: </label><input type='select' name='town' length=25></li>
        <li><label for='postcode'>Postcode: </label><input type='select' name='postcode' length=25></li>
        <li><label for='ni'>NI number: </label><input type='select' name='ni' length=25></li>
        <li><label for='nhs'>NHS Number: </label><input type='select' name='nhs'  length=25></li>
      </ul>
      <button id='btnAdvSearch' type='submit'>Search</button>
    </form>

  </div> <!-- advanced -->

  </div> <!-- tab-content -->
  <div id="results" class="col-md-24" style="min-height:250px">
    <!-- Search results are inserted here -->
  </div>
</div> <!-- well -->

@section('pagescript')
  <script src="/resources/orchid/js/jquery.oLoader.min.js"></script>
  <script src="/resources/orchid/js/select.js"></script>

@stop

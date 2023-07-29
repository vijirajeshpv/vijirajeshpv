<?php
  include_once "inc/header.php";
  include_once "inc/sidebar.php";
?>


       <script>
        function calculateEMI() {
            
            var item_description=document.myform.item_description.value;
              if (!item_description)
                item_description = null;
            
            var gross_weight = document.myform.gross_weight.value;
            if (!gross_weight)
                gross_weight = '0';

             var stone_weight = document.myform.stone_weight.value;
            if (!stone_weight)
                stone_weight = '0';
            


            var market_value =document.myform.market_value.value;
            if (!market_value)
                market_value = '0';

            
            var gross_weight = parseFloat(gross_weight);
            var stone_weight = parseFloat(stone_weight);
            var net_weight   =gross_weight-stone_weight;

            var loan_amount = net_weight*(market_value*(80/100));
            document.myform.net_weight.value = parseFloat(net_weight).toFixed(2);
            document.myform.loan_amount.value=parseFloat(loan_amount);

        }
      </script>


  <?php 
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_loan_application'])) {
        $inserted = $ml->applyForLoan($_POST,$_FILES);
        }
   ?>
        <h3 class="page-heading mb-4">Gold Loan application form</h3>
        <h5 class="card-title p-3 bg-info text-white rounded">Fill up loan details</h5>
        <div class="container">
          <?php
          if (isset($inserted)){
          ?>
          <div id="successMessage" class="alert alert-success alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              <?php  echo $inserted; ?>
         </div>

          <?php
            }
          ?> 


          <?php 
           $name = "";
$b_id = "";


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    $searchName = $_POST['key']; // Assuming the input field name is 'key'
    
    $br = $emp->findBorrowerByName($searchName); // Assuming you have a method 'findBorrowerByName' to search by name
    if ($br) {
        $row = $br->fetch_assoc();
        $name = $row['name'];
echo $name;
        $b_id = $row['id'];
    } else {
        echo "<span class='text-center' style='color:red'>Borrower not found or not applicable for a loan</span>";
    }
}
        
           ?>

          <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
                <div class="form-group row">
              <label for="inputBorrowerFirstName" class="text-right col-2 font-weight-bold col-form-label">Search brrower: </label>                      
              <div class="col-sm-6">
                  <input type="text" name="key" class="form-control" id="inputBorrowerFirstName" placeholder="Enter Name of borrower" required>
              </div>
              <div class="col-sm-3">
                <input type="submit" class="btn btn-info" name="search" value="Search">
              </div>  
            </div>

          </form>  

          <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data" name="myform" id="myform" >
            </div>
<!--Added Borrower Name-->
            <div class="form-group row">
              <label for="borrower_name" class="text-right col-2 font-weight-bold col-form-label">Borrower Name</label>                      
              <div class="col-sm-9">
                  <input type="text"  name="borrower_name" class="form-control" value="<?php echo $name; ?>" readonly>
              </div>
            </div>

            <!--Added Borrower ID-->
            <div class="form-group row">
              <label for="borrower_id" class="text-right col-2 font-weight-bold col-form-label">Borrower ID</label>                      
              <div class="col-sm-9">
                  <input type="text"  name="b_id" class="form-control" value="<?php echo $b_id; ?>" readonly>
              </div>
            </div>

           

            <div class="form-group row">
              <label for="item_description" class="text-right col-2 font-weight-bold col-form-label">Item description</label>                      
              <div class="col-sm-9">
                 
<!--Item Description-->

<label for="item_description">Select an item:</label>

<select id="item_description" name="item_description[]" multiple>
  <option value="Chain">Chain</option>
  <option value="Bangle">Bangle</option>
  <option value="Ring">Ring</option>
  <option value="ear ring">ear ring</option>
</select>
              </div>
            </div>

            <div class="form-group row">
              <label for="gross_weight" class="text-right col-2 font-weight-bold col-form-label">gross_weight</label>                      
              <div class="col-sm-9">
                  <input type="number" onkeyup="calculateEMI()" name="gross_weight" class="form-control" id="gross_weight" min="0"  step="0.01" placeholder="Enter gross_weight" required>
              </div>
            </div>

            <div class="form-group row">
                <label  class="text-right col-2 font-weight-bold col-form-label">Stone weight</label>                      
                 <div class="col-sm-9">
                  <input type="number" onkeyup="calculateEMI()" name="stone_weight" class="form-control" id="stone_weight" min="0"  step="0.01" placeholder="Enter Stone weight" required>
              </div>
            </div> 
            
             <div class="form-group row">
                <label  class="text-right col-2 font-weight-bold col-form-label">Net Weight</label>                      
                 <div class="col-sm-9">
                  <input type="text"  id="net_weight" name="net_weight" class="form-control" readonly required>
              </div>
            </div> 

            <div class="form-group row">
                <label for="market_value" class="text-right col-2 font-weight-bold col-form-label">Market Value</label>  
                <div class="col-sm-9">
                    <input type="number" onkeyup="calculateEMI()" name="market_value" class="form-control positive-integer" id="market_value"  required>
                </div>
            </div>
<div class="form-group row">
                <label for="loan_amount" class="text-right col-2 font-weight-bold col-form-label">Loan Amount</label>  
                <div class="col-sm-9">
                    <input type="text" name="loan_amount" class="form-control positive-integer" id="loan_amount"  required>
                </div>
            </div>
          <hr>
          <div class="form-group row">
              <label for="borrower_files" class="text-right font-weight-bold col-2 col-form-label">Borrower Files<br>(doc, docx, and pdf only)</label>
              <div class="col-sm-9">    
                  <input type="file"  name="borrower_files" required>
              </div>
          </div>
             <hr>
          <div class="form-group row">
              <div class="col-md-6">
              <input type="submit" name="submit_loan_application" class="btn btn-info pull-right" value="Submit Application">
              </div>
          </div><!-- /.box-footer -->    
        </form>
       </div>       

     
<?php
include_once "inc/footer.php";
?>
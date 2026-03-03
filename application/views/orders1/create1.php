<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <section class="content-header">
    <h1>
      Manage
      <small>Orders</small>
    </h1>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">

        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">Create Order</h3>
          </div>

           <form role="form" action="<?php base_url('orders1/create1') ?>" method="post" class="form-horizontal">
            

            <div class="box-body">

              <?php echo validation_errors(); ?>

              <!-- Customer Name -->
              <div class="form-group">
                <label class="col-sm-3 control-label">Customer Name</label>
                <div class="col-sm-6">
                  <input type="text" name="customer_name" class="form-control" required>
                </div>
              </div>

              <!-- Customer Address -->
              <div class="form-group">
                <label class="col-sm-3 control-label">Customer Address</label>
                <div class="col-sm-6">
                  <textarea name="customer_address" class="form-control" rows="3" required></textarea>
                </div>
              </div>

              <!-- Customer Phone -->
              <div class="form-group">
                <label class="col-sm-3 control-label">Customer Phone</label>
                <div class="col-sm-6">
                  <input type="text" name="customer_phone" class="form-control" required>
                </div>
              </div>

              <!-- No PO -->
              <div class="form-group">
                <label class="col-sm-3 control-label">No PO</label>
                <div class="col-sm-6">
                  <input type="text" name="no_po" class="form-control">
                </div>
              </div>

              <!-- Tanggal PO -->
              <div class="form-group">
                <label class="col-sm-3 control-label">Tanggal PO</label>
                <div class="col-sm-6">
                  <input type="date" name="tanggal_po" class="form-control">
                </div>
              </div>

              <!-- No DO -->
              <div class="form-group">
                <label class="col-sm-3 control-label">No DO</label>
                <div class="col-sm-6">
                  <input type="text" name="no_do" class="form-control">
                </div>
              </div>

              <!-- Tanggal DO -->
              <div class="form-group">
                <label class="col-sm-3 control-label">Tanggal DO</label>
                <div class="col-sm-6">
                  <input type="date" name="tanggal_do" class="form-control">
                </div>
              </div>

              <!-- Nama Barang -->
              <div class="form-group">
                <label class="col-sm-3 control-label">Nama Barang</label>
                <div class="col-sm-6">
                  <input type="text" name="nama_barang" class="form-control" required>
                </div>
              </div>

              <!-- Gambar -->
              <div class="form-group">
                <label class="col-sm-3 control-label">Gambar</label>
                <div class="col-sm-6">
                  <input type="file" name="gambar" class="form-control">
                </div>
              </div>

              <!-- Keterangan -->
              <div class="form-group">
                <label class="col-sm-3 control-label">Keterangan</label>
                <div class="col-sm-6">
                  <textarea name="keterangan" class="form-control" rows="3"></textarea>
                </div>
              </div>

            </div>

            
              <div class="box-footer">
                <input type="hidden" name="service_charge_rate" value="<?php echo $company_data['service_charge_value'] ?>" autocomplete="off">
                <input type="hidden" name="vat_charge_rate" value="<?php echo $company_data['vat_charge_value'] ?>" autocomplete="off">
                <button type="submit" class="btn btn-primary">Create Order</button>
                <a href="<?php echo base_url('orders1/') ?>" class="btn btn-warning">Back</a>
              </div>

          </form>

        </div>
      </div>
    </div>
  </section>
</div>
<template>
  <div class="container mt-5 mb-5">
    <div class="d-flex align-items-center mb-4">
      <i class="bi bi-truck text-primary fs-1 me-3"></i>
      <h2 class="mb-0 fw-bold">Szállítás és Fizetés</h2>
    </div>

    <div class="row g-4">
      <div class="col-lg-6">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-header bg-primary text-white py-3">
            <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i> Szállítási módok</h5>
          </div>
          <div class="card-body">
            <div class="list-group list-group-flush">
              <div class="list-group-item px-0 py-3">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <h6 class="fw-bold mb-1">Házhozszállítás futárszolgálattal</h6>
                    <small class="text-muted">Kényelmes átvétel az otthonában (1-3 munkanap)</small>
                  </div>
                  <span class="badge bg-light text-primary border border-primary rounded-pill p-2">1 990 Ft</span>
                </div>
              </div>

              <div class="list-group-item px-0 py-3">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <h6 class="fw-bold mb-1">Csomagpontos átvétel (Foxpost)</h6>
                    <small class="text-muted">Átvétel az Önnek megfelelő időpontban (2-4 munkanap)</small>
                  </div>
                  <span class="badge bg-light text-primary border border-primary rounded-pill p-2">1 190 Ft</span>
                </div>
              </div>

              <div class="list-group-item px-0 py-3 border-bottom-0">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <h6 class="fw-bold mb-1 text-success">Személyes átvétel a műhelyben</h6>
                    <small class="text-muted">Sopron, Kézműves utca 12. (H-P: 10:00-18:00)</small>
                  </div>
                  <span class="badge bg-success rounded-pill p-2">Ingyenes</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-header bg-dark text-white py-3">
            <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i> Fizetési opciók</h5>
          </div>
          <div class="card-body">
            <div class="alert alert-warning border-0 shadow-sm mb-4 d-flex">
              <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
              <div>
                <strong>Vizsgaprojekt információ:</strong>
                <p class="small mb-0">Az oldal csak demonstrációs célokat szolgál. Valódi pénzügyi tranzakció nem történik, adatai nincsenek veszélyben.</p>
              </div>
            </div>

            <div class="list-group list-group-flush">
              <div class="list-group-item px-0 py-2 d-flex align-items-center border-0">
                <i class="bi bi-cash-stack text-muted me-3 fs-4"></i>
                <div>
                  <span class="fw-bold d-block">Utánvét</span>
                  <small class="text-muted">Fizessen készpénzzel vagy kártyával a futárnak.</small>
                </div>
              </div>
              <div class="list-group-item px-0 py-2 d-flex align-items-center border-0">
                <i class="bi bi-shield-check text-muted me-3 fs-4"></i>
                <div>
                  <span class="fw-bold d-block">Online bankkártyás fizetés</span>
                  <small class="text-muted">Biztonságos fizetés (Stripe / Barion szimuláció).</small>
                </div>
              </div>
              <div class="list-group-item px-0 py-2 d-flex align-items-center border-0">
                <i class="bi bi-bank text-muted me-3 fs-4"></i>
                <div>
                  <span class="fw-bold d-block">Előre utalás</span>
                  <small class="text-muted">Bankszámlaszám: 12345678-12345678-00000000</small>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer bg-light border-0 py-3">
            <small class="text-muted"><i class="bi bi-info-circle me-1"></i> A kiszállítás a rendelés visszaigazolását követően indul el.</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ShippingView'
}
</script>

<style scoped>
.card {
  transition: transform 0.2s;
}
.card:hover {
  transform: translateY(-5px);
}
</style>
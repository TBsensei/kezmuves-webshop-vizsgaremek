<template>
  <div class="container mt-5 mb-5">
    <h2 class="mb-4">Szállítás és Fizetés</h2>
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <h5>📦 Szállítási módok</h5>
            <ul class="list-group list-group-flush mb-4">
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Házhozszállítás futárszolgálattal
                <span class="badge bg-primary rounded-pill">1 990 Ft</span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Csomagpontos átvétel (Foxpost)
                <span class="badge bg-primary rounded-pill">1 190 Ft</span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Személyes átvétel a műhelyben
                <span class="badge bg-success rounded-pill">Ingyenes</span>
              </li>
            </ul>
          </div>
          <div class="col-md-6">
            <h5>💳 Fizetési módok</h5>

            <div class="alert alert-warning py-2 small mb-3">
              <strong>Figyelem!</strong> Ez a weboldal egy vizsgaremek projekt. Valódi értékesítés és fizetési tranzakció nem történik!
            </div>

            <ul class="list-group list-group-flush">
              <li class="list-group-item">Utánvét (Készpénz vagy bankkártya a futárnál)</li>
              <li class="list-group-item">Online bankkártyás fizetés (Stripe / Barion)</li>
              <li class="list-group-item">Előre utalás bankszámlára</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
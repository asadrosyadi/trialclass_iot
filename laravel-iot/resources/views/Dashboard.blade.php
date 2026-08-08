<!DOCTYPE html>
<html lang="id" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Sensor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.3.0/raphael.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/justgage/1.4.0/justgage.min.js"></script>
</head>

<body class="bg-body-tertiary">

    <div class="container py-5">
        <div class="bg-primary bg-gradient text-white rounded-4 shadow p-4 p-md-5 mb-4 d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <p class="text-uppercase small fw-semibold mb-1 opacity-75">IoT &middot; Live Monitoring</p>
                <h1 class="fw-bold mb-0">Data Sensor DHT &amp; Potensio</h1>
            </div>
            <span class="badge bg-white text-primary rounded-pill fs-6 px-3 py-2" id="connBadge">Menghubungkan&hellip;</span>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card text-center shadow-sm border-danger rounded-4 h-100">
                    <div class="card-body">
                        <div class="bg-danger-subtle text-danger rounded-circle d-inline-flex align-items-center justify-content-center fs-3 mb-2" style="width: 56px; height: 56px;">🌡️</div>
                        <h6 class="text-uppercase text-secondary small fw-semibold">Suhu</h6>
                        <div id="gaugeSuhu" style="width: 100%; max-width: 200px; height: 200px; margin: 0 auto;"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-center shadow-sm border-info rounded-4 h-100">
                    <div class="card-body">
                        <div class="bg-info-subtle text-info rounded-circle d-inline-flex align-items-center justify-content-center fs-3 mb-2" style="width: 56px; height: 56px;">💧</div>
                        <h6 class="text-uppercase text-secondary small fw-semibold">Kelembapan</h6>
                        <div id="gaugeKelembapan" style="width: 100%; max-width: 200px; height: 200px; margin: 0 auto;"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-center shadow-sm border-warning rounded-4 h-100">
                    <div class="card-body">
                        <div class="bg-warning-subtle text-warning rounded-circle d-inline-flex align-items-center justify-content-center fs-3 mb-2" style="width: 56px; height: 56px;">🎚️</div>
                        <h6 class="text-uppercase text-secondary small fw-semibold">Potensio (ADC)</h6>
                        <div id="gaugePotensio" style="width: 100%; max-width: 200px; height: 200px; margin: 0 auto;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3 p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-warning-subtle text-warning rounded-circle d-inline-flex align-items-center justify-content-center fs-3" style="width: 56px; height: 56px;" id="bulbIcon">💡</div>
                    <div>
                        <h6 class="text-uppercase text-secondary small fw-semibold mb-1">Status LED</h6>
                        <span class="badge fs-6 text-bg-secondary" id="ledStatus">&mdash;</span>
                    </div>
                </div>
                <button id="toggleLed" class="btn btn-primary btn-lg rounded-pill px-4">Memuat&hellip;</button>
            </div>
        </div>

        <p class="text-center text-secondary small mt-4">Dashboard IoT &middot; data diperbarui otomatis setiap 5 detik</p>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var gaugeSuhu, gaugeKelembapan, gaugePotensio;

        function initGauges() {
            var common = {
                relativeGaugeSize: true,
                counter: true,
                gaugeWidthScale: 0.55,
                shadowOpacity: 0
            };

            gaugeSuhu = new JustGage($.extend({}, common, {
                id: "gaugeSuhu",
                value: 0,
                min: -10,
                max: 50,
                symbol: "°C",
                levelColors: ["#0dcaf0", "#ffc107", "#dc3545"]
            }));

            gaugeKelembapan = new JustGage($.extend({}, common, {
                id: "gaugeKelembapan",
                value: 0,
                min: 0,
                max: 100,
                symbol: "%",
                levelColors: ["#dc3545", "#ffc107", "#0dcaf0"]
            }));

            gaugePotensio = new JustGage($.extend({}, common, {
                id: "gaugePotensio",
                value: 0,
                min: 0,
                max: 1024,
                levelColors: ["#6c757d", "#0d6efd", "#ffc107"]
            }));
        }

        var API_BASE = "{{ url('/api') }}";

        function setLedUi(status) {
            var isOn = status == 'ON';
            $("#ledStatus").text(status).toggleClass('text-bg-success', isOn).toggleClass('text-bg-secondary', !isOn);
            $("#bulbIcon").text(isOn ? "💡" : "🔌");
            $("#toggleLed").text(isOn ? "Matikan LED" : "Nyalakan LED")
                .toggleClass('btn-primary', !isOn)
                .toggleClass('btn-outline-secondary', isOn);
        }

        function setConnBadge(isUp) {
            var text = isUp
                ? "Diperbarui " + new Date().toLocaleTimeString('id-ID')
                : "Koneksi terputus";
            $("#connBadge").text(text)
                .toggleClass('bg-white text-primary', isUp)
                .toggleClass('text-bg-danger', !isUp);
        }

        function ambilDataSensor() {
            $.getJSON(API_BASE + "/sensor-readings/latest", function(response) {
                var sensorData = response.data;

                if (sensorData.suhu) {
                    gaugeSuhu.refresh(sensorData.suhu);
                }

                if (sensorData.kelembapan) {
                    gaugeKelembapan.refresh(sensorData.kelembapan);
                }

                if (sensorData.potensio) {
                    gaugePotensio.refresh(sensorData.potensio);
                }

                setConnBadge(true);
            }).fail(function() {
                setConnBadge(false);
            });

            $.getJSON(API_BASE + "/led", function(response) {
                setLedUi(response.data.status);
            });
        }

        $("#toggleLed").click(function() {
            $.post(API_BASE + "/led/toggle", function(response) {
                setLedUi(response.data.status);
            });
        });

        $(document).ready(function() {
            initGauges();
            ambilDataSensor();
            setInterval(ambilDataSensor, 5000);
        });
    </script>

</body>

</html>

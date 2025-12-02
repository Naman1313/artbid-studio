// ========== SIGNATURE PAD ==========
let canvas, ctx, drawing = false;

function initSignaturePad() {
    canvas = document.getElementById("signature");
    if (!canvas) return;

    ctx = canvas.getContext("2d");

    canvas.addEventListener("mousedown", () => drawing = true);
    canvas.addEventListener("mouseup", () => drawing = false);
    canvas.addEventListener("mouseleave", () => drawing = false);

    canvas.addEventListener("mousemove", draw);

    document.getElementById("clear-signature").addEventListener("click", () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    });
}

function draw(e) {
    if (!drawing) return;
    ctx.lineWidth = 2;
    ctx.lineCap = "round";
    ctx.strokeStyle = "#000";

    let rect = canvas.getBoundingClientRect();
    ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
    ctx.stroke();
    ctx.beginPath();
    ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
}

// ========== BID SUBMISSION ==========
$(document).on("click", "#place-bid", function () {
    const artId = $(this).data("art");
    const amount = $("#bid-amount").val();

    // Get signature data URL
    let sig = canvas.toDataURL("image/png");

    $("#bid-status").html("<div class='notice'>Submitting...</div>");

    $.post("place_bid.php", {
        art_id: artId,
        amount: amount,
        signature: sig
    }, function (res) {
        if (res.status === "success") {
            $("#bid-status").html("<div class='notice success'>" + res.msg + "</div>");
        } else {
            $("#bid-status").html("<div class='notice error'>" + res.msg + "</div>");
        }
    }, "json");
});

// ========== BID POLLING (XML) ==========
function startBidPolling(artId) {
    setInterval(() => {
        $.ajax({
            url: "get_current_bid.php?id=" + artId,
            dataType: "xml",
            success: function (xml) {
                const value = $(xml).find("current").text();
                $("#current-price").text(parseFloat(value).toFixed(2));
            }
        });
    }, 2000);
}

// Initialize signature pad (only when present)
$(document).ready(() => {
    initSignaturePad();
});

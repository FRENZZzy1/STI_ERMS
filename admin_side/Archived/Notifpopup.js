let lastId = 0;

// Preload sound
function playNotifSound() {
  const sound = new Audio("notif.mp3");
  sound.play().catch(err => console.log("Sound blocked:", err));
}

// Initialize lastId with latest notification when page loads
fetch("Notifs_retrieve.php")
  .then(r => r.json())
  .then(d => { if (d.id) lastId = d.id; });

function checkNotifications() {
  fetch("Notifs_retrieve.php") // make sure filename matches your PHP script
    .then(response => response.json())
    .then(data => {
      if (data.id && data.id != lastId) {
        lastId = data.id; // update last seen id
        showPopup("📢 New Report!<br>" +
          "Equipment: " + data.equipment + "<br>" +
          "Reason: " + data.reason + "<br>" +
          "Location: " + data.location + "<br>" +
          "By: " + data.username);
      }
    })
    .catch(err => console.error("Fetch error:", err));
}

function showPopup(message) {
  playNotifSound();

  // create popup container
  const popup = document.createElement("div");
  popup.innerHTML = `
    <div style="
      background:#333;
      color:#fff;
      padding:30px;
      border-radius:15px;
      box-shadow:0 4px 12px rgba(0,0,0,0.3);
      text-align:center;
      font-size:18px;
      max-width:400px;
    ">
      <p>${message}</p>
      <button id="popupOkBtn" style="
        margin-top:15px;
        padding:10px 20px;
        font-size:16px;
        border:none;
        border-radius:8px;
        background:#007bff;
        color:#fff;
        cursor:pointer;
      ">OK</button>
    </div>
  `;

  // style for centering
  popup.style.position = "fixed";
  popup.style.top = "50%";
  popup.style.left = "50%";
  popup.style.transform = "translate(-50%, -50%)";
  popup.style.zIndex = "9999";

  // add to body
  document.body.appendChild(popup);

  // handle button click
  document.getElementById("popupOkBtn").onclick = () => popup.remove();
}

// Check every 5 seconds
setInterval(checkNotifications, 5000);

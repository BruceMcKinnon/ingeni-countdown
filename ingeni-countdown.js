jQuery(document).ready(function() {
  const second = 1000,
        minute = second * 60,
        hour = minute * 60,
        day = hour * 24;

  var target = document.getElementById("ingeni_countdown_target").innerText;
  
  const countDown = new Date(target).getTime(),
      x = setInterval(function() {    

        const now = new Date().getTime(),
        distance = countDown - now;

        document.getElementById("ingeni_countdown_days").innerText = Math.floor(distance / (day)),
        document.getElementById("ingeni_countdown_hours").innerText = Math.floor((distance % (day)) / (hour)),
        document.getElementById("ingeni_countdown_minutes").innerText = Math.floor((distance % (hour)) / (minute)),
        document.getElementById("ingeni_countdown_seconds").innerText = Math.floor((distance % (minute)) / second);
      }, 0);
});


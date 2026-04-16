/* *****************
 * Classe pour la main page du kiosk Sauvetage d'Ouchy
 * **************** */

class souchy_kiosk {
  constructor() {
    // === CONFIGURATION des pages ===
    this.current = 0; // index page courante
    this.PLAYLIST = []; // Lite des pages (sera initialisée depuis fichier config

    // === CONFIGURATION Rotation des Pages ===
    this.timer = null; // setInterval pour la rotation
    this.rotating = true; // rotation active
    this.autoResumeMin = 30; // Délai de reprise (minutes) de la rotation si elle est coupée
    this.resumeTimeout = null; // setTimeout pour reprise auto

    // === CONFIGURATION Night Mode ===
    this.nightmode = false; // mode nuit inactive
    this.nightmodeCheckMin = 15; // Délai de vérification pour timer de activation du night mode au besoin
    this.NIGHT_START = { h: 0, m: 1 }; // Début du mode nuit
    this.NIGHT_END = { h: 7, m: 0 }; // Fin du mode nuit

    // === DOM ===
    this.elToolbar = document.getElementById("toolbar");
    this.elFrame = document.getElementById("kioskFrame");
    this.elPagesC = document.getElementById("pages");
    this.elToggle = document.getElementById("toggle");
    this.elStatus = document.getElementById("status");
    this.elNightOverlay = document.getElementById("night-overlay");
    this.enforceNightState = this.enforceNightState.bind(this); // Binder pour le callback
  }

  // Get the configuration for the pages
  // (based on deviceID that is passed as coockie)
  async get_config() {
    const res = await fetch("/kiosk/kiosk_api.php", {
      method: "POST",
      headers: {
        Accept: "application/html",
        "Content-Type": "application/json",
        "X-Timestamp": Date.now(),
      },
      body: JSON.stringify({ actionkey: "config" }),
    });
    if (res.status == 500) {
      return {
        playlist: [
          {
            url: "/kiosk/kiosk_error.php?error=" + res.status,
            seconds: 9999999,
            button: "ERROR a",
          },
        ],
      };
    }
    const data = await res.json();
    if (!data.success) {
      return {
        playlist: [
          {
            url: "/kiosk/kiosk_error.php?error=" + data.error,
            seconds: 9999999,
            button: "ERROR b",
          },
        ],
      };
    }
    return data.config;
  }

  // === FONCTIONS ===
  buildPageButtons() {
    if (!this.elPagesC) return;
    this.elPagesC.innerHTML = "";
    this.PLAYLIST.forEach((item, i) => {
      const b = document.createElement("button");
      b.className = "page-btn";
      b.textContent = `${item.button}`;
      b.title = `Aller à la page ${item.button} (désactive la rotation)`;
      b.dataset.index = i;
      b.addEventListener("click", () => {
        this.stopRotation();
        this.load(i);
      });
      this.elPagesC.appendChild(b);
    });
  }

  markActiveButton() {
    if (!this.elPagesC) return;
    for (const btn of this.elPagesC.querySelectorAll(".page-btn")) {
      btn.classList.toggle(
        "active",
        Number(btn.dataset.index) === this.current
      );
    }
  }

  // Charge la i ème page du kiosk avec un effet fade in fade out
  load(i) {
    if (!this.elFrame) return;

    const n = this.PLAYLIST.length;
    this.current = ((i % n) + n) % n;

    // Annimation de Sortie -----------------------------------------
    this.elFrame.classList.remove("pre-slide-in-left", "slide-in-left");
    void this.elFrame.offsetWidth;
    this.elFrame.classList.add("slide-out-left");

    // déterminer l'URL suivante
    let url = "";
    if (this.PLAYLIST[this.current].url) {
      url = this.PLAYLIST[this.current].url;
      this.elContent = "";
    } else {
      const error = this.PLAYLIST[this.current].content;
      url = "/kiosk/kiosk_error.php?error=" + encodeURIComponent(error);
    }

    //  Quand la nouvelle page est chargée
    const onLoad = () => {
      // -> Animation d'entreé ------
      this.elFrame.removeEventListener("load", onLoad);
      this.elFrame.classList.remove("slide-out-left");
      this.elFrame.classList.add("pre-slide-in-left"); // position initiale sans transition
      void this.elFrame.offsetWidth; // reflow
      this.elFrame.classList.remove("pre-slide-in-left");
      this.elFrame.classList.add("slide-in-left"); // lance l’entrée

      // suite l
      this.markActiveButton();
      this.updateStatus();
      if (this.rotating)
        this.scheduleTimer(this.PLAYLIST[this.current].seconds);
    };
    this.elFrame.addEventListener("load", onLoad, { once: true });

    setTimeout(() => {
      this.elFrame.src = url;
    }, 20);
  }

  next() {
    this.load(this.current + 1);
  }

  // === Gestion Rotation===

  startRotation() {
    if (this.rotating) return;
    this.rotating = true;
    this.scheduleTimer(1);
    this.updateToggleUI();
    this.updateStatus();
    this.autoResume(false);
  }

  stopRotation() {
    this.rotating = false;
    if (this.timer) {
      clearInterval(this.timer);
      this.timer = null;
    }
    this.updateToggleUI();
    this.updateStatus();
    this.autoResume(true);
  }

  toggleRotation() {
    this.rotating ? this.stopRotation() : this.startRotation();
  }

  scheduleTimer(rotationSeconds) {
    if (this.timer) clearInterval(this.timer);
    if (!this.rotating) return;
    this.timer = setInterval(() => this.next(), rotationSeconds * 1000);
  }

  updateToggleUI() {
    if (!this.elToggle) return;
    this.elToggle.classList.toggle("on", this.rotating);
    this.elToggle.classList.toggle("off", !this.rotating);
    this.elToggle.textContent = this.rotating
      ? "⏸️ Arrêter la rotation"
      : "▶️ Démarrer la rotation";
  }

  updateStatus() {
    if (!this.elStatus) return;
    this.elStatus.textContent = `${
      this.rotating ? "Rotation active" : "Rotation arrêtée"
    } • Page ${this.current + 1}/${this.PLAYLIST.length}`;
  }

  autoResume(arm) {
    if (this.resumeTimeout) {
      clearTimeout(this.resumeTimeout);
      this.resumeTimeout = null;
    }
    if (arm) {
      const delayMs = this.autoResumeMin * 60 * 1000;
      this.resumeTimeout = setTimeout(() => {
        this.startRotation();
      }, delayMs);
    }
  }

  // === Gestion Mode nuit ===

  // Active le mode nuit
  showNight() {
    if (!this.elNightOverlay) return;
    // on se repositionne sur la première page statique pour eviter les pages a loading régulier
    this.stopRotation();
    this.current = 0;
    this.next();
    // On met l'overlay night mode
    this.elNightOverlay.classList.remove("night-hidden");
    this.elNightOverlay.classList.add("night-visible");
    this.nightmode = true;
    this.autoResume(false);
    this.elToolbar.classList.add("night");
    this.elFrame.classList.add("night");
  }

  // Desactive l emode nuit
  hideNight() {
    if (!this.elNightOverlay) return;
    this.elNightOverlay.classList.remove("night-visible");
    this.elNightOverlay.classList.add("night-hidden");
    this.nightmode = false;
    this.elToolbar.classList.add("day");
    this.elFrame.classList.add("day");
    this.startRotation();
  }

  // Vérifie et active ou desactive le mode nuit au besoin
  enforceNightState() {
    const isNight = () => {
      const d = new Date();
      const cur = d.getHours() * 60 + d.getMinutes();
      return (
        cur >= this.NIGHT_START.h * 60 + this.NIGHT_START.m &&
        cur < this.NIGHT_END.h * 60 + this.NIGHT_END.m
      );
    };
    if (isNight() && !this.nightmode) this.showNight();
    if (!isNight() && this.nightmode) this.hideNight();
    return {"isNight":isNight()};
  }

  // Réveil par interaction — */
  handleWakeEvent(ev) {
    if (this.nightmode) this.hideNight();
  }

  // === utilitaires / Divers ===

  updateClock() {
    if (this.elClock) {
      const now = new Date();
      const opts = { hour: "2-digit", minute: "2-digit" };
      this.elClock.innerHTML = now.toLocaleTimeString("fr-CH", opts);
    }
  }

  // === INIT ===
  async init() {
    const cfg = await this.get_config();
    this.PLAYLIST = cfg.playlist;
    this.buildPageButtons();
    this.load(this.current);
    this.updateToggleUI();

    if (this.elToggle) {
      this.elToggle.addEventListener("click", () => this.toggleRotation());
    }

    // active la vérification toutes les 15 minutes du mode nuit
    this.nightModeTimerId = setInterval(
      this.enforceNightState,
      this.nightmodeCheckMin * 60 * 1000
    );
    this.elNightOverlay.addEventListener("mousedown", () =>
      this.handleWakeEvent()
    );
    this.elNightOverlay.addEventListener("touchstart", () =>
      this.handleWakeEvent()
    );

    // Pause auto si l’onglet est masqué
    document.addEventListener("visibilitychange", () => {
      if (document.hidden && this.rotating) {
        this.stopRotation();
      }
    });
  }
}

// Exemple d’utilisation :
// const kiosk = new souchy_kiosk();
// kiosk.init();
// window.kiosk = kiosk; // optionnel pour débogage

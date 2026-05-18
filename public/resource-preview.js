document.addEventListener('DOMContentLoaded', function() {
    // --- SELECTION DES ELEMENTS DU DOM ---
    
    // Inputs du formulaire
    const inputName = document.getElementById('input-name');
    const inputCategory = document.getElementById('input-category');
    const inputStatus = document.getElementById('input-status');
    const inputSpecs = document.getElementById('input-specs');
    const inputImage = document.getElementById('input-image');

    // Elements de l'aperçu
    const previewName = document.getElementById('preview-name');
    const previewCategory = document.getElementById('preview-category');
    const previewStatusBadge = document.getElementById('preview-status-badge');
    const previewStatusText = document.getElementById('preview-status-text');
    const previewSpecs = document.getElementById('preview-specs');
    const previewImage = document.getElementById('preview-image');

    // Valeurs par défaut (pour quand les champs sont vides)
    const defaultName = "Nom du produit";
    const defaultSpecs = "Spécifications...";
    // Remplace par le chemin de ton image de cube par défaut si tu en as une
    const defaultImageSrc = previewImage.src; 

    // --- FONCTIONS DE MISE A JOUR ---

    // 1. Mise à jour du NOM
    inputName.addEventListener('input', function() {
        previewName.textContent = this.value.trim() !== '' ? this.value : defaultName;
    });

    // 2. Mise à jour de la CATEGORIE
    inputCategory.addEventListener('change', function() {
        // On prend le texte de l'option sélectionnée, pas la value
        previewCategory.textContent = this.options[this.selectedIndex].text.toUpperCase();
    });

    // 3. Mise à jour du STATUT (Texte + Couleur)
    inputStatus.addEventListener('change', function() {
        const status = this.value;
        // Mise à jour du texte (on enlève l'emoji)
        const statusText = this.options[this.selectedIndex].text.replace(/✅|🔧|🔒/g, '').trim();
        previewStatusText.textContent = statusText;

        // Mise à jour de la classe CSS pour la couleur
        previewStatusBadge.className = 'card-status-badge'; // Reset
        if (status === 'available') {
            previewStatusBadge.classList.add('status-available');
        } else if (status === 'maintenance') {
            previewStatusBadge.classList.add('status-maintenance');
        } else if (status === 'reserved') {
            previewStatusBadge.classList.add('status-reserved');
        }
    });

    // 4. Mise à jour des SPECIFICATIONS
    inputSpecs.addEventListener('input', function() {
        previewSpecs.textContent = this.value.trim() !== '' ? this.value : defaultSpecs;
    });

    // 5. Mise à jour de l'IMAGE (Le plus complexe)
    inputImage.addEventListener('change', function() {
        const file = this.files[0];
        const fileCustomLabel = this.nextElementSibling; // Le span "Choisir un fichier..."

        if (file) {
            // Mettre à jour le nom du fichier dans l'input custom
            fileCustomLabel.textContent = file.name;

            // Lire le fichier et l'afficher dans l'aperçu
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                // On enlève la classe "default-cube" pour que la vraie image soit en pleine taille
                previewImage.classList.remove('default-cube'); 
            }
            
            reader.readAsDataURL(file);
        } else {
            // Si on désélectionne, on remet l'image par défaut
            previewImage.src = defaultImageSrc;
            previewImage.classList.add('default-cube');
            fileCustomLabel.textContent = "Choisir un fichier...";
        }
    });

    // --- INITIALISATION AU CHARGEMENT ---
    // Pour que l'aperçu soit correct si le navigateur a pré-rempli des champs
    inputName.dispatchEvent(new Event('input'));
    inputCategory.dispatchEvent(new Event('change'));
    inputStatus.dispatchEvent(new Event('change'));
    inputSpecs.dispatchEvent(new Event('input'));

    function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const output = document.getElementById('live-preview');
        output.src = reader.result;
        output.style.display = 'block';
    };
    reader.readAsDataURL(event.target.files[0]);
}
});
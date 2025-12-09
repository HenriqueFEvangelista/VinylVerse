<script>
function configurarEscapeParaSair() {
    
    document.addEventListener('keydown', (event) => {
        
        if (event.key === 'Escape') {
            
            event.preventDefault(); 

            const btnSair = document.getElementById('btnSair');
            if (btnSair) {
                
                btnSair.click();
            } else {
                console.warn("Aviso: O botão com ID 'btnSair' não foi encontrado.");
                
            }
        }
    });
}

configurarEscapeParaSair();
</script>
const app = Vue.createApp({
    data() {
        return {
            gameTitle: "RPG de Mesa",
            id_user: 1,
            id_personagem: 1,
            dd_personagem: [],
            playerCoin: "0",
            atributos: [],
            armas: [],
            itens: [],
            locations: [],
            // dialogos            
            dialogos: [], // Recebido do banco de dados
            currentIndex: 0,
            audio: null,
            isPlaying: false,
            // x dialogos
            id_mapa_pai: 0,
            batalha: false,
            atacar: false,
            log_batalha: "",
            showModalBat: false,
            showModalWin: false,
            showModalLose: false,
            dadoAtaque: 0,
            dadoDefesa: 0,
            dado: 15, // Valor do dado
            // log do combate
            // ataque
            dado_atack: 0, // Força do personagem
            forca_atack: 0, // Força do personagem
            defesaOponente_atack: 0, // Defesa do oponente
            danoTotal_atack: 0, // Dano calculado (exemplo)
            currentStep_atack: 0, // Controla o passo atual da animação

            // defesa
            dado_atackOpo: 0, // Força do personagem
            forca_atackOpo: 0, // Força do personagem
            defesaOponente_atackOpo: 0, // Defesa do oponente
            danoTotal_atackOpo: 0, // Dano calculado (exemplo)
            currentStep_atackOpo: 0, // Controla o passo atual da animação

            // x log do combate
            //backgroundSrc: "https://chatbot-whatsapp-br.com.br/imagens/rpg/map-background.jpg", // caminho inicial do background
            backgroundSrc: "https://chatbot-whatsapp-br.com.br/imagens/rpg/mapa2.png", // caminho inicial do background 
            selectedLocation: null,
            selectedBatalha: [],
            bgSound: null, // Vai armazenar o elemento de áudio            
            isSoundPlaying: false, 

            // Dados do jogador
            playerName: "Jogador 1",
            playerBalance: "1000",
            playerOtherData: "Nível: 1, Pontos de Vida: 200/200",

            baseUrl: "https://chatbot-whatsapp-br.com.br/",
            baseUrlImg: "https://chatbot-whatsapp-br.com.br/imagens/rpg/",
            imgPersonagem: "",

            itensWin: [],
            xpWin: 0,

            // dados (batalha)
            isModalPers: false,
            isModalOpen: false,
            selectedArma: null,
            diceResults: [],

            // itens            
            showModalUseItem: false,
            itemUse: [],
        };
    },
    mounted() {
        this.getLocais(1);
        //this.selectLocation(10)
        //this.getLocais(1);
        //this.selectLocation(10);
        this.getPersonagem(this.id_personagem);
        this.bgSound = document.getElementById('bgSound'); // Acessa o áudio do fundo
        this.bgSound.volume = 0.5; // Ajusta o volume inicial, por exemplo
        //this.bgSound = document.getElementById('bgSound'); // Acessa o elemento de áudio
        if (this.bgSound) {
            //this.playSound('bgSound');
            this.bgSound.play()
            console.log(this.bgSound);
            this.bgSound.volume = 0.5; // Configura o volume do som de fundo
            this.bgSound.loop = true;   // Define para tocar em loop
            //this.bgSound.play();        // Reproduz o som de fundo automaticamente
        }else{
            console.log("SEm SOM");
        }

        this.getArmas(this.id_user,this.id_personagem);
        this.getItens(this.id_user,this.id_personagem);

        this.getLastLocal(this.id_personagem);
        //this.playSound('bgSound');

        //this.getLocais(10);
        //this.playSound('entrando');

        
        //this.setLocal(10); // gnomo
        //this.setLocal(20); // floresta
        //this.setLocal(50); // Mapa da floresta

        // this.bgSound = document.getElementById('bgSound');
        // if (this.bgSound) {
        //     this.bgSound.volume = 0.5; // Ajuste o volume conforme necessário
        //     this.bgSound.loop = true; // Garante o loop contínuo
        // }
        //this.playSound('bgSound')

        //this.resumeBgSound()
    },
    methods: {

        saveProgress(progressData) {
            fetch(this.baseUrl + 'rpg/save_progress', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(progressData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    console.log(data.message);
                } else {
                    console.error('Erro ao salvar progresso:', data.message);
                }
            })
            .catch(error => {
                console.error('Erro na requisição:', error);
            });
        },
        goHome(){
            this.getLocais(1);
            this.playSound('entrando');
            this.batalha = false;
            //this.backgroundSrc = this.selectedLocation.bg;
            this.backgroundSrc = this.baseUrlImg+"mapa2.png";
        },
        getLastLocal(id_personagem){
            
            fetch(this.baseUrl+'rpg/get_last_location/'+id_personagem)
            .then(response => response.json())
            .then(data => {
                if(data[0]){
                    var lastLocal = data[0];
                    console.log("lastLocal");
                    console.log(lastLocal);
                    //return false;
                    
                    if(lastLocal.batalha == 1){
                        console.log("com batalha");
                        this.setLocal(lastLocal.id_mapa,1)
                        // .then(function(){
                        //     //this.enterBat()
                        //     console.log("depois de enterBat");    
                        // }) 
                        //this.enterBat()
                    }else{
                        this.setLocal(lastLocal.id_mapa)
                        // .then(function(){
                        //     //this.enterBat()
                        //     console.log("depois de setLocal");    
                        // })    
                        
                        console.log("sem batalha");
                    }
                }else{
                    this.setLocal(1);    
                }
                
            });
        },
        getLocais(id_mapa){            
            fetch(this.baseUrl+'rpg/get_locations/'+id_mapa)
            .then(response => response.json())
            .then(data => {
                this.locations = data;
            });
        },
        getDialogos(id_mapa){
            fetch(this.baseUrl+'rpg/get_dialogos/'+id_mapa)
            .then(response => response.json())
            .then(data => {
                this.dialogos = data;
                console.log(this.dialogos);
            });
        },
        toggleAudio() {
          if (!this.audio) {
            //this.audio = new Audio(this.dialogos[this.currentIndex].audio);
            //this.audio = new Audio(this.dialogos.audio);
            this.audio = new Audio(`./sons/dialogos/${this.dialogos[this.currentIndex].audio}`);

          }

          if (this.isPlaying) {
            this.audio.pause();
          } else {
            //this.audio.currentTime = 0;
            this.audio.play();
          }

          this.isPlaying = !this.isPlaying;
        },
        updateAudio() {
          if (this.audio) {
            this.audio.pause(); // Pausa o áudio anterior
          }
          //this.audio = new Audio(this.dialogos[this.currentIndex].audio);
          this.audio = new Audio(this.dialogos.audio);
          this.isPlaying = false; // Reseta o botão
        },

        setLocal(id_mapa,batalha=0){            
            fetch(this.baseUrl+'rpg/get_location/'+id_mapa)
            .then(response => response.json())
            .then(data => {
                //this.locations = data;
                //this.selectedLocation = data[0];
                this.selectLocation(data[0])
                this.enterLocation();

                if(this.selectLocation.bat == 1){
                    this.enterBat()
                }
                //alert(this.selectLocation.id_mapa)
                
            });
        },
        getPersonagem(id_pers){            
            fetch(this.baseUrl+'rpg/get_dd_personagem/'+id_pers)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    const personagem = data[0]; // Pega o primeiro personagem do array
                    this.dd_personagem = personagem;
                    this.playerName = personagem.nome;
                    this.playerBalance = personagem.life;
                    this.imgPersonagem = this.baseUrlImg+"personagens/"+personagem.img;
                    //playerOtherData: "Nível: 1, Pontos de Vida: 200/200",
                    this.playerOtherData = `Nível: ${personagem.nivel}, Pontos de Vida: ${personagem.life}/${personagem.life_total}`;
                    console.log('IMAGEM do personagem:', this.imgPersonagem);
                    console.log('Dados do personagem:', personagem);
                } else {
                    console.log('Nenhum personagem encontrado.');
                }                
                
            });
        },
        getArmas(id_user,id_personagem){            
            fetch('https://chatbot-whatsapp-br.com.br/rpg/get_armas/'+id_user+'/'+id_personagem+'/arma')
            .then(response => response.json())
            .then(data => {
                
                this.armas = data;
                console.log(this.armas);
            });
        },
        getItens(id_user,id_personagem){            
            fetch('https://chatbot-whatsapp-br.com.br/rpg/get_armas/'+id_user+'/'+id_personagem+'/consumo')
            .then(response => response.json())
            .then(data => {
                
                this.itens = data;
                console.log("ITENS: ",this.itens);
            });
        },
        selectLocation(location) {
            //this.playSound('buttonSound');
            this.selectedLocation = location;
            this.playSound('modalSound');
            //this.batalha = (location.bat == 0) ? false;
            if(location.bat == 0){ this.batalha = false }
        },
        playHoverSound() {
            this.playSound('hoverSound',0.7);
        },
        playBackgroundSound() {
            this.playSound('bgSound');
            //this.playSound('bgSound');
            // const bgSound = this.$refs['bgSound'];
            // this.bgSound = document.getElementById('bgSound');
            // if (this.bgSound) {
            //     this.bgSound.play().then(() => {
            //         this.isSoundPlaying = true; // Atualiza o estado quando o som começa a tocar
            //         console.log("PLAY TRUE");
            //     }).catch(error => {
            //         console.error("Erro ao reproduzir o som de fundo:", error);
            //     });

            // }
        },
        enterBat(){
            alert(`Você entrou na batalha com ${this.selectedLocation.name}! Próximo local ${this.selectedLocation.name}! `);
            this.playSound('entrando');
            this.batalha = true;
            this.locations = this.getLocais(this.selectedLocation.id)
            this.backgroundSrc = this.baseUrlImg+this.selectedLocation.bg; // Caminho do novo mapa
            this.dialogos = [];
            
            //this.selectedBatalha = null; // Remove o modal após a mudança
            // this.selectedBatalha = location;
            //this.bgSound.stop();
            this.playSound('bgbatalhaSound');
            this.getArmas(this.id_user);


            var dd_progress = {
                id_mapa: this.selectedLocation.id,
                id_user: this.id_user,
                id_oponente: this.selectedLocation.id_personagem,
                id_personagem: this.id_personagem,
                batalha: 1,
                // progress: {
                //     level: 10,
                //     exp: 5000
                // }
            };
            this.saveProgress(dd_progress);


            setTimeout(() => {
              this.showModalBat = true;
              //this.selectedBatalha = true;
            }, 5000); // Delay de 500ms

            

            this.getBatalha(this.id_personagem,this.selectedLocation.id_personagem);

            // this.bgSound = document.getElementById('bgbatalhaSound');
            // this.bgSound.play();
            // this.selectedLocation = null;
            this.closeModal();
        },
        getBatalha(id_personagem,id_oponente){            
            fetch(this.baseUrl+'rpg/get_batalha/'+id_personagem+'/'+id_oponente)
            .then(response => response.json())
            .then(data => {
                this.selectedBatalha = data[0];
                if(this.selectedBatalha.id_personagem_acao == this.id_personagem){
                    this.atacar = true;
                }else{
                    this.atacar = false;
                }
                console.log("dados da batalha")
                console.log(data[0])
                if(this.selectedBatalha.hp_restante_oponente <= 0 && this.batalha == true){
                    
                    this.$refs['winning'].play();
                    this.log_batalha = "Você VENCEU!";
                    this.showModalBat = false;
                    this.selectedBatalha =  [];
                    this.atacar = false;
                    this.batalha = false;
                    this.getItens(this.id_user,this.id_personagem);
                    //this.showModalWin = true;
                    this.verificaItens(id_oponente,id_personagem);
                    // .then(response => (call_itens){
                    //     console.log('call_itens', call_itens);
                    // })
                    return false;
                }

                if(this.selectedBatalha.hp_restante_personagem <= 0 && this.batalha == true){
                    
                    this.$refs['gameover'].play();
                    this.$refs['gameover2'].play();

                    this.log_batalha = "Você PERDEU!";
                    this.showModalBat = false;
                    this.selectedBatalha =  [];
                    this.atacar = false;
                    this.batalha = false;
                    this.showModalLose = true;
                    // finaliza batalha
                    fetch(this.baseUrl+'rpg/end_batalha/'+id_personagem+'/'+id_oponente)
                    .then(response => {
                        console.log(response.json());
                    })
                    //this.verificaItens(id_oponente,id_personagem);
                    // .then(response => (call_itens){
                    //     console.log('call_itens', call_itens);
                    // })
                    return false;
                }
                this.getPersonagem(this.id_personagem);
            });
        },
        verificaItens(id_oponente,id_personagem){
            this.dado_atack = null; // Força do personagem
            this.forca_atack = null; // Força do personagem
            this.defesaOponente_atack = null; // Defesa do oponente
            this.danoTotal_atack = null; // Dano calculado (exemplo)
            this.currentStep_atack = null; // Controla o passo atual da animação

            // defesa
            this.dado_atackOpo = null; // Força do personagem
            this.forca_atackOpo = null; // Força do personagem
            this.defesaOponente_atackOpo = null; // Defesa do oponente
            this.danoTotal_atackOpo = null; // Dano calculado (exemplo)
            this.currentStep_atackOpo = null; // Controla o passo atual da animação  

            var turnoData = {
                id_oponente: id_oponente,
                id_personagem: id_personagem,               
            };     

            fetch(this.baseUrl + 'rpg/verificaItens', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(turnoData)
                })
                .then(response => response.json())
                .then(data => {
                    this.itensWin = data.itens;
                    if(data.itens){
                        this.playSound('soundForca');
                        this.showModalWin = true;
                        this.getPersonagem(this.id_personagem);
                        this.getArmas(this.id_user,this.id_personagem);
                    }
                    if(data.error){
                        alert(data.error)
                    }
                    this.xpWin = data.exp;
                    console.log('data intens', data);
                    
                })



        },
        enterLocation() {
            console.log('selectedLocation', this.selectedLocation);
            alert(`Você entrou em ${this.selectedLocation.name}!`);
            this.playSound('entrando');
            this.locations = this.getLocais(this.selectedLocation.id)
            this.id_mapa_pai = this.selectedLocation.id_mapa;

            this.dialogos = this.getDialogos(this.selectedLocation.id);

            //alert("ID PAI: "+this.id_mapa_pai);
           
            // Atualizar o background do mapa
            //this.backgroundSrc = "https://chatbot-whatsapp-br.com.br/imagens/rpg/vila-anoes.jpg"; // Caminho do novo mapa
            //this.backgroundSrc = this.selectedLocation.icon; // Caminho do novo mapa
            this.backgroundSrc = "https://chatbot-whatsapp-br.com.br/imagens/rpg/"+this.selectedLocation.bg; // Caminho do novo mapa

            if(this.selectedLocation.bat == "0"){
                this.verificaItens(this.selectedLocation.id_personagem,this.id_personagem);
            }
            

            var dd_progress = {
                id_mapa: this.selectedLocation.id,
                id_user: this.id_user,
                id_personagem: this.id_personagem,
                batalha: this.selectedLocation.bat,
                // progress: {
                //     level: 10,
                //     exp: 5000
                // }
            };
            this.saveProgress(dd_progress);

            this.selectedLocation = null; // Remove o modal após a mudança

            this.closeModal();
        }, // x enterLocation
        nextLocation(id_next){ 
            alert("NEXT");
        },
        closeModal(som=false) {
            this.selectedLocation = null;
            if(som == true){
                this.playSound('bt_close');
            }
            
        },
        closeModalWin(){
            this.showModalWin = false;
            this.log_batalha = "";
        },
        closeModalLose(){
            this.showModalLose = false;
        },
        closeModalDialogo(){
            this.dialogos = [];
        },
        
        closeModalBat(som=false) {
            this.isModalOpen = false;
            this.showModalBat = false;
            this.diceResults = [];
            //this.selectedBatalha = null;
            this.selectedBatalha = [];
            if(som == true){
                this.playSound('bt_close');
            }
            this.log_batalha = "";
            
        },
        //  ARMAS PARA BATALHA
        // playHoverSound() {
        //   // Som ao passar o mouse (opcional)
        // },
        openModal(arma) {
          this.selectedArma = arma;
          this.isModalOpen = true;
          this.rollDice();
        },
        openModalPersonagem(id_pers){
            this.isModalPers = true;
            this.playHoverSound();
            //alert(id_pers);
        },
        closeModalPers(){
            this.isModalPers = false;
        },
        rolarDado(lados) {
            var n_dado = Math.floor(Math.random() * lados) + 1; 
            n_dado = parseInt(n_dado)
            return n_dado;
        },
        abrirItem(item){
            this.showModalUseItem = true;
            this.itemUse = item;
        },
        usarItem(item,id_personagem){
            //alert(item_id+" - "+id_personagem)
            console.log("Item: ", item);
            console.log("id_personagem: ", id_personagem);
            // this.showModalUseItem = true;
            // this.itemUse = item;
            //return false;
            //////////////////////////////

            var itemData = {
                id_personagem: id_personagem,
                item_id: item.item_id,                
            };
            console.log("itemData");
            console.log(itemData);

              fetch(this.baseUrl + 'rpg/usar_item', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(itemData)
                    //body: itemData
                })
                //.then(response => response.json())
                .then(data => {
                    if (data) {
                        console.log(data)
                        this.showModalUseItem = false;
                        this.getPersonagem(this.id_personagem);
                        //this.getPersonagem(this.id_personagem);
                        this.getItens(this.id_user,this.id_personagem);
                    }
                })
            //////////////////////////////



        },
        fechaModalUseItem(){
            //this.modalV = false;
            this.showModalUseItem = false;
        },
        voltarModal(modalV){
            this.modalV = false;
        },
        // Teste de ataque
        ataque(selectedBatalha) {
          this.getBatalha(selectedBatalha.id_personagem,selectedBatalha.id_oponente); // atualiza dados da batalha
          this.atacar = false;
          this.dado_atack = null;
          this.forca_atack = null;
          this.danoTotal_atack = null;
          this.defesaOponente_atack = null;

          

          //this.selectedArma = arma;
          //this.isModalOpen = true;
          this.rollDice();
          //const dado = this.rolarDado(20); // Rola um D20
          //var defesa = 2;
          var defesa = this.selectedBatalha.o_defesa;
          this.defesaOponente_atack = defesa;
          var dado = this.rolarDado(6); // Rola um D6
          this.dadoAtaque = dado;
          var forca_int = parseInt(this.dd_personagem.forca);
          var dano = (dado + forca_int) - defesa ;
          dano = parseInt(dano);

          if(dano < 0){
            dano = 0;
          }
          

          // this.dado_atack = dado;
          // this.forca_atack = forca_int;
          // this.danoTotal_atack = dano;
          //this.log_batalha = `Dano: ${dado} + ${this.dd_personagem.forca} de Força = ${dano} `;
          this.log_batalha = `Ataque: Dado + ${this.dd_personagem.forca} de Força `;

          this.log_batalha = `Ataque user: ${selectedBatalha.p_forca}  defesa oponente( ${selectedBatalha.o_defesa} defesa: [${defesa}] `;

          // set turno e passa vez
          var turnoData = {
                id_batalha: selectedBatalha.id,
                dado_rolado: dado,
                dano: dano,
            };
            console.log("turnoData");
            console.log(turnoData);

              fetch(this.baseUrl + 'rpg/passa_turno', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(turnoData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        this.getBatalha(selectedBatalha.id_personagem,selectedBatalha.id_oponente); // atualiza dados da batalha
                        console.log("PASSA TURNO");
                        console.log(data);
                        //this.getBatalha(selectedBatalha.id_personagem,selectedBatalha.id_oponente); // atualiza dados da batalha
                        //this.iniciarAtaque(dado, forca_int, dano, defesa)
                        // Zera os valores inicialmente
                          // this.dado_atack = null;
                          // this.forca_atack = null;
                          // this.danoTotal_atack = null;
                          //this.defesaOponente_atack = null;

                          let segundos = 0; // Contador de segundos
                          const intervalo = setInterval(() => {
                            segundos++;

                            if (segundos === 1) {

                              // Mostra o valor do dado e toca o som
                              this.dado_atack = dado;
                              //this.$refs['soundDado'].play();
                              this.$refs[selectedBatalha.p_som_ataque].play();
                              
                              //this.$refs['soundForca'].play();
                            }

                            if (segundos === 3) {
                              // Mostra o valor da força e toca o som
                              this.forca_atack = forca_int;
                              this.$refs['soundForca'].play();
                            }

                            if (segundos === 4) {
                              // Mostra a defesa do oponente e toca o som
                              //this.defesaOponente_atack = defesa;
                              //this.defesaOponente_atack
                              this.$refs['soundDefesa'].play();
                            }

                            if (segundos === 5) {
                              // Mostra o dano total e toca o som
                              this.danoTotal_atack = dano;
                              // if((dado + forca_int) > defesa){
                              if(dano  > 0){
                                this.$refs['soundDano'].play();  
                              }else{
                                this.$refs['soundDefesa'].play();  
                              }

                              // console.log("OPONENTE HP "+this.selectedBatalha.hp_restante_oponente);
                              //   if(this.selectedBatalha.hp_restante_oponente <= 0){
                              //       this.log_batalha = "Você VENCEU!";
                              //       this.showModalBat = false;
                              //       this.selectedBatalha =  [];
                              //       this.atacar = false;
                              //       this.batalha = false;
                              //       //this.showModalWin = true;
                              //       clearInterval(intervalo);            
                              //       return false;
                              //   }


                              
                            } // x 5 segundos e verifica fim


                            if (segundos >= 7) {
                              // Para o setInterval após 10 segundos
                              clearInterval(intervalo);

                              if(this.batalha == false){
                                return false;
                              }

                              //////// RODADA PC
                              //defesa = parseInt(this.dd_personagem.defesa);
                              var defesa = parseInt(selectedBatalha.p_defesa);
                              dado = this.rolarDado(6); // Rola um D6
                              this.dadoAtaque = dado;
                              //forca_int = parseInt(this.dd_personagem.forca);
                              //forca_int = 3;
                              forca_int = parseInt(selectedBatalha.o_forca);
                              dano = (dado + forca_int) - defesa ;
                              if(dano < 0){
                                dano = 0;
                              }
                              dano = parseInt(dano);

                            // set turno e passa vez
                              var turnoDataPC = {
                                    id_batalha: selectedBatalha.id,
                                    dado_rolado: dado,
                                    dano: dano,                         
                                };

                                fetch(this.baseUrl + 'rpg/passa_turno', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify(turnoDataPC)
                                })
                                .then(response => response.json())
                                .then(data => {

                                    //this.iniciarAtaque(dado, forca_int, dano, defesa)

                                    if (data) {
                                        console.log("PASSA TURNO PC");
                                        console.log(data);
                                        //this.atacar = true;
                                        this.atacar = false;
                                        this.getBatalha(selectedBatalha.id_personagem,selectedBatalha.id_oponente); // atualiza dados da batalha

                                    }

                                })
                                .catch(error => {
                                    console.error('Erro na requisição PC:', error);
                                });
                                console.log("turnoData PC");
                                console.log(turnoDataPC);
                                var segundosPC = 0;
                                const intervaloPC = setInterval(() => {
                                    segundosPC++;
                                    if(segundosPC == 2){
                                        this.atacar = false;
                                        if(this.batalha == false){
                                            return false;
                                        }
                                        this.log_batalha = `Ataque do oponente: ${selectedBatalha.o_forca} Sua defesa( ${selectedBatalha.p_defesa} )`;
                                        //this.iniciarAtaque(dado, forca_int, dano, defesa)
                                    }




                                    if(segundosPC == 3){
                                        this.$refs[this.selectedBatalha.o_som_ataque].play();
                                        this.iniciarAtaqueOpo(dado, forca_int, dano, defesa)
                                    }

                                    if (segundosPC === 6) {
                                        this.log_batalha = `Fim do turno, jogue novamente`;
                                        this.atacar = true;
                                        
                                        //this.atacar = true;
                                        clearInterval(intervaloPC);
                                    } // x if 6
                                }, 1000); // Executa a cada 1 segundo
                                

                                



                            //////// X X RODADA PC

                            } // x segundos 10
                          }, 1000); // Executa a cada 1 segundo

                        


                    } else {
                        console.error('Erro ao salvar TURNO JOGAODR:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro na requisição:', error);
                });

                

          return dado;
          //return dado + ataque > defesa;
        },
        iniciarAtaque(dado, forca_int, dano, defesa) {
          // Zera os valores inicialmente
          this.dado_atack = null;
          this.forca_atack = null;
          this.danoTotal_atack = null;
          this.defesaOponente_atack = null;

          let segundos = 0; // Contador de segundos
          const intervalo = setInterval(() => {
            segundos++;

            if (segundos === 2) {
              // Mostra o valor do dado e toca o som
              this.dado_atack = dado;
              this.$refs['soundDado'].play();
              //this.$refs['soundForca'].play();
            }

            if (segundos === 3) {
              // Mostra o valor da força e toca o som
              this.forca_atack = forca_int;
              this.$refs['soundForca'].play();
            }

            if (segundos === 4) {
              // Mostra a defesa do oponente e toca o som
              this.defesaOponente_atack = defesa;
              this.$refs['soundDefesa'].play();
            }

            if (segundos === 5) {
              // Mostra o dano total e toca o som
              this.danoTotal_atack = dano;
              //if((dado + forca_int) > defesa){
              if((dano + forca_int) > defesa){
                this.$refs['soundDano'].play();  
              }else{
                this.$refs['soundDefesa'].play();  
              }

              // if(this.selectedBatalha.hp_restante_oponente <= 0){
              //       this.log_batalha = "Você VENCEU!";
              //       this.showModalBat = false;
              //       this.selectedBatalha =  [];
              //       this.atacar = false;
              //       this.batalha = false;
              //       //this.showModalWin = true;
              //       this.verificaItens(id_oponente,id_personagem);             
              //       return false;
              //   }
              
            } // x 5 segundos e verifica fim

            if (segundos >= 6) {
              // Para o setInterval após 10 segundos
              clearInterval(intervalo);
            }
          }, 1000); // Executa a cada 1 segundo
        },
        iniciarAtaqueOpo(dado, forca_int, dano, defesa) {
          // Zera os valores inicialmente
          // defesa
          //Opo
          this.dado_atackOpo = null;
          this.forca_atackOpo = null;
          this.danoTotal_atackOpo = null;
          this.defesaOponente_atackOpo = null;

          let segundos = 0; // Contador de segundos
          const intervalo = setInterval(() => {
            segundos++;

            if (segundos === 2) {
              // Mostra o valor do dado e toca o som
              this.dado_atackOpo = dado;
              //this.$refs['soundDado'].play();
              
              //this.$refs['soundForca'].play();
            }

            if (segundos === 3) {
              // Mostra o valor da força e toca o som
              this.forca_atackOpo = forca_int;
              this.$refs['soundForca'].play();
            }

            if (segundos === 4) {
              // Mostra a defesa do oponente e toca o som
              this.defesaOponente_atackOpo = defesa;
              this.$refs['soundDefesa'].play();
            }

            if (segundos === 5) {
              // Mostra o dano total e toca o som
              this.danoTotal_atackOpo = dano;
              if((dado + forca_int) > defesa){
                this.$refs['soundDano'].play();  
              }else{
                this.$refs['soundDefesa'].play();  
              }
              
            }

            if (segundos >= 10) {
              // Para o setInterval após 10 segundos
              clearInterval(intervalo);
            }
          }, 1000); // Executa a cada 1 segundo
        },

        rollDice() {
          // Toca o som de fundo          
          this.playSound('diceSound');
          


          // Animação dos dados
          const rolls = [];
          let count = 0;
          const interval = setInterval(() => {
            rolls.length = 0; // Limpa o array para animação
            for (let i = 0; i < 3; i++) {
              rolls.push(Math.floor(Math.random() * 6) + 1); // Dado de 6 lados
              console.log(rolls)
            }
            //this.diceResults = [...rolls];
            this.diceResults = [rolls];

            count++;
            if (count >= 2) {
              clearInterval(interval);
              //diceSound.pause();
              //diceSound.currentTime = 0; // Reseta o som
            }
          }, 100);
        },
        // X ARMAS PARA BATALHA

        playSound(refName,tempo=0) {
            const sound = this.$refs[refName];
            if (sound) {
                if(tempo == 0){
                    sound.currentTime = 0; // Reinicia o som
                }else{
                    sound.currentTime = tempo; // Reinicia o som
                }
                
                sound.play();
                console.log('play: ', refName);
            }
        },
        // pauseBgSound() {
        //     if (this.bgSound) {
        //         this.bgSound.pause(); // Pausa o som de fundo
        //     }
        // },
        // resumeBgSound() {
        //     if (this.bgSound) {
        //         this.bgSound.play(); // Resumir o som de fundo
        //     }
        // },
    },
});

app.mount('#app');

��    ]           �      �     �     �     �                 
   3     >  �  L     �	  (   
  '   7
     _
  E   x
     �
     �
     �
     �
     �
       
             &  -  >  	   l     v     �  	   �     �     �     �  �   �     u     �  3   �     �    �  �   	  �   �  �   4  �   �     �  j   �          !     A     R     W     f     }  @   �  �   �  �   �  %        ?     R     W  Z   j  �   �     �     �     �     �     �     �          -     F     R     a     q     ~     �  
   �     �     �     �     �  	             .     F     Z  7   ]     �  %     =   ;  d   y     �  	   �  +   �  �     e    
     
   �     �     �     �     �  
   �  	   �  6  �     -  3   C  D   w     �  C   �          /     ;      H     i     u     �     �     �  �  �     @      H      a      z      �      �      �   �   �      :!     T!  8   m!     �!  N  �!  �   #  �   �#  �   |$  �   N%     9&  k   A&     �&  '   �&     �&     '     
'      '     >'  .   S'  �   �'  �   I(  0   �(     )     )     )  q   5)  �   �)  $   �*  (   �*  %   �*     +  	   +      +  "   ;+      ^+     +     �+     �+     �+     �+     �+  
   �+     �+  !    ,     ",     A,  
   Q,     \,     s,     �,     �,  U   �,  k    -  !   l-  J   �-  v   �-     P.  	   X.  .   b.  N  �.     W       5   1           +       ?             "         X              *   3                      8      Z       6   '                B       L   I   E          >           K   ]   :   O       J   7   4   =   	   <          %   G   
       &      ;   Q         S   !   C          A           .   ,   [   $           U            )   N   \   R                      #   2                      F          9   /   Y               D   M       V   H   @   (   P   -   T      0       (Add) (Edit) (pick extension) *-prim Add Follow Me Add Follow Me Settings Alert Info Announcement: By default (not checked) any call to this extension will go to this Follow-Me instead, including directory calls by name from IVRs. If checked, calls will go only to the extension.<BR>However, destinations that specify FollowMe will come here.<BR>Checking this box is often used in conjunction with VmX Locater, where you want a call to ring the extension, and then only if the caller chooses to find you do you want it to come here. CID Name Prefix Cannot connect to Asterisk Manager with  Checking if recordings need migration.. Choose a user/extension: Choose an extension to append to the end of the extension list above. Confirm Calls Default Delete Entries Destination if no answer Disable Edit %s Edit %s %s Edit Follow Me Edit Follow Me Settings Enable this if you're calling external numbers that need confirmation - eg, a mobile phone may go to voicemail which will pick up the call. Enabling this requires the remote side push 1 on their phone before the call is put through. This feature only works with the ringall/ringall-prim  ring strategy Extension Extension Quick Pick Findme Follow Toggle Follow Me Follow-Me List Follow-Me User: %s Follow-Me: %s (%s) If you select a Music on Hold class to play, instead of 'Ring', they will hear that instead of Ringing while they are waiting for someone to pick up. Initial Ring Time: Invalid Group Number specified Invalid prefix. Valid characters: a-z A-Z 0-9 : _ - Invalid time specified List extensions to ring, one per line, or use the Extension Quick Pick below.<br><br>You can include an extension on a remote system, or an external number by suffixing a number with a pound (#).  ex:  2448089# would dial 2448089 on the appropriate trunk (see Outbound Routing). Message to be played to the caller before dialing this group.<br><br>To add additional recordings please use the "System Recordings" MENU to the left Message to be played to the caller before dialing this group.<br><br>You must install and enable the "Systems Recordings" Module to edit this option Message to be played to the person RECEIVING the call, if 'Confirm Calls' is enabled.<br><br>To add additional recordings use the "System Recordings" MENU to the left Message to be played to the person RECEIVING the call, if the call has already been accepted before they push 1.<br><br>To add additional recordings use the "System Recordings" MENU to the left None Only ringall, ringallv2, hunt and the respective -prim versions are supported when confirmation is checked Play Music On Hold? Please enter an extension list. Remote Announce: Ring Ring Strategy: Ring Time (max 60 sec) Submit Changes The number users will dial to ring extensions in this ring group This is the number of seconds to ring the primary extension prior to proceeding to the follow-me list. The extension can also be included in the follow-me list. A 0 setting will bypass this. Time in seconds that the phones will ring. For all hunt style ring strategies, this is the time for each iteration of phone(s) that are rung Time must be between 1 and 60 seconds Too-Late Announce: User Warning! Extension You can optionally include an Alert Info which can create distinctive rings on SIP phones. You can optionally prefix the Caller ID name when ringing extensions in this group. ie: If you prefix with "Sales:", a call from John Doe would display as "Sales:John Doe" on the extensions that ring. adding annmsg_id field.. adding remotealert_id field.. adding toolate_id field.. already migrated deleted dropping annmsg field.. dropping remotealert field.. dropping toolate field.. fatal error firstavailable firstnotonphone group number hunt is not allowed for your account memoryhunt migrate annmsg to ids.. migrate remotealert to ids.. migrate toolate to  ids.. migrated %s entries migrating no annmsg field??? no remotealert field??? no toolate field??? ok ring all available channels until one answers (default) ring first extension in the list, then ring the 1st and 2nd extension, then ring 1st 2nd and 3rd extension in the list.... etc. ring only the first available channel ring only the first channel which is not off hook - ignore CW ring primary extension for initial ring time followed by all additional extensions until one answers ringall ringallv2 take turns ringing each available extension these modes act as described above. However, if the primary extension (first in list) is occupied, the other extensions will not be rung. If the primary is FreePBX DND, it won't be rung. If the primary is FreePBX CF unconditional, then all will be rung Project-Id-Version: 2.5
Report-Msgid-Bugs-To: 
POT-Creation-Date: 2008-10-05 23:10+0200
PO-Revision-Date: 2008-11-10 11:52+0100
Last-Translator: Francesco Romano <francesco.romano@alteclab.it>
Language-Team: Italian
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
X-Poedit-Language: Italian
X-Poedit-Country: ITALY
 (Aggiungi) (Modifica) (scegliere l'interno) *-prim Aggiungi Seguimi Modifica impostazioni Seguimi Alert Info Annuncio: Nell'impostazione predefinita (non selezionato) tutte le chiamate su questo interno andranno al Seguimi, incluse le chiamate da un IVR. Se selezionato, le chiamate andranno solo all'interno.<br>Questa opzione è di solito usata insieme al VmX Locater, dove si puo' scegliere se inviare il chiamante al seguimi. Prefisso ID Chiamante Impossibile connettersi al manager di Asterisk con  Sto controllando se le registrazione necessitano di una migrazione.. Scegliere un utente/interno: Scegliere un interno da aggiungere alla fine della lista qui sopra. Conferma Chiamate Predefinito Elimina voci Destinazione se nessuna risposta Disattivato Modifica %s Modifica %s %s Modifica Seguimi Modifica impostazioni Seguimi Attivare questa opzione se si vogliono chiamare numeri esterni che hanno bisogno di conferma - es., un telefono cellulare potrebbe andare ad una segreteria, e in quel caso la chiamata sarà presa. Attivando questa opzione l'utente remoto dovrà digitare 1 sul proprio telefono per accettare la chiamata. Questa opzione funziona solo con le strategie di squillo ringall e ringall-prim. Interno Selezione Veloce Interno Attiva/Disattiva Seguimi Seguimi Lista Seguimi Utente Seguimi: %s Seguimi: %s (%s) Se si seleziona una classe di Musica di Attesa, invece che 'Squillo', l'utente ascolterà questa mentre è in attesa di una risposta. Tempo inziale di squillo: Numero Gruppo non valido Prefisso non valido. Caratteri validi: a-z A-Z 0-9 : _ - Tempo specificato non valido Inserire gli interni o numeri da chiamare, uno per riga, o utilizzare la Selezione Veloce degli Interni qui sotto.<br><br>Per includere numeri esterni, inserire cancelletto (#) alla fine del numero. Es.: per chiamare 06123456789 bisgona inserire 006123456789# (se nelle Rotte in uscita è stato inserito lo 0 per le chiamate esterne). Messaggio da riprodurre al chiamante prima di chiamare questo gruppo.<br><br>Per aggiungere ulteriori registrazioni utilizzare "Registrazioni di Sistema" nel MENU di sinistra Messaggio da rirprodurre al chiamante prima di chiamare questo gruppo.<br><br>Per utilizzare questa opzione, bisogna aver prima installato e attivato il modulo "Registrazioni di Sistema" Il messaggio da riprodurre alla persona che RICEVE la chiamata, se è stato attivato 'Conferma Chiamate'<br><br>Per aggiungere ulteriori registrazioni utilizzare "Registrazioni di Sistema" nel MENU di sinistra Il messaggio da riprodurre alla persona che RICEVE la chiamata, se la chiamata è già stata accettata prima di premere il tasto.<br><br>Per aggiungere ulteriori registrazioni utilizzare "Registrazioni di Sistema" nel MENU di sinistra Nessuno Solo ringall, ringallv2, hunt e le respettive versioni -prim sono supportate quando la conferma è attivata Riproduci Musica di Attesa? Prego immettere la lista degli interni. Annuncio Remoto: Squillo Strategia di Squillo: Tempo di squillo (max 60 sec) Conferma Cambiamenti I numeri che saranno chiamati in questo gruppo Questo è il numero di secondi di squillo per l'interno primario prima di far procedere la chiamata nella lista seguimi. L'interno può anche essere incluso nella lista. 0 per passarlo direttamente. Il tempo in secondi che un telefono squilla. Per i gruppi di chiamata con strategia hunt, equivale allo squillo di ogni singolo interno Il tempo deve essere compreso tra 1 e 60 secondi Annuncio Troppo-Tardi Utente Attenzione! L'Interno Si può anche includere come opzione un messaggio Alert Info per distinguere le suonerie su apparati di tipo SIP. Come opzione si puo' inserire un prefisso prima dell'identificativo chiamante. Es.: se si inserisce "Commerciale:", una chiamata per Mario Rossi sarà visualizzata come "Commerciale:Mario Rossi" sul display del telefono che squilla. sto aggiungendo il campo annmsg_id.. sto aggiungendo il campo remotealer_id.. sto aggiungendo il campo toolate_id.. gia migrato Eliminato sto scartando il campo annmsg... sto scartadno il campo remotealert sto scartando il campo toolate.. errore fatale firstavailable firstnotonphone Numero gruppo hunt non possiede i permessi memoryhunt migrazione annmsg verso ids.. migrazione remotealer verso ids.. migrazione toolate verso ids.. migrate %s voci migrazione nessun campo annmsg??? nessun campo remotealert??? nessun campo toolate??? ok chiama tutti i canali disponibili fino a quando un interno non risponde (predefinito) chiama il primo interno della lista, poi il primo e il secondo, poi il primo, il secondo e il terzo... ecc. squilla solo il primo disponibile squilla solo il primo che non è al telefono - ignora l'Avviso di Chiamata squilla l'interno primario per il tempo iniziale di squillo seguito dagli altri interni fino a quando uno non risponde ringall ringallv2 chiama a circolo tutti gli interni disponibili queste modalità sono attuate come descritto sopra. Però, se l'interno primario (il primo della lista è occupato, gli altri interni non saranno chiamati. Se il primario ha attivato il Non-Disturbare di FreePBX, non andrà avanti. Se il primario è un Trasferimento di Chiamata incondizionato attivato su FreePBX, tutti squilleranno. 
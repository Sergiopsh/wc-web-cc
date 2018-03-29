��    "      ,  /   <      �     �     
     !     9     L     \     i     r    y  R  �     �     �     �  �        �  R   �     6     F  
   Z     e     r     �  �   �  %   q	  $   �	  0   �	  �   �	     �
  -   �
  �  $     �     �  t   �  e  n     �     �                0     B     R     ^    e    z     ~     �     �    �     �  q   �     *     <     S     _     l     {  �   �  (   �  #   �  0   �           R      !  s     �     �  w   �                                                        	                                            "                                   
   !                    Add Paging Group Conflicting Extensions Default Group Inclusion Default Page Group Delete Group %s Device List: Disabled Duplex Each PBX system can have a single Default Page Group. If specified, extensions can be automatically added (or removed) from this group in the Extensions (or Devices) tab.<br />Making this group the default will uncheck the option from the current default group if specified. Example usage:<br /><table><tr><td><strong>%snnn</strong>:</td><td>Intercom extension nnn</td></tr><tr><td><strong>%s</strong>:</td><td>Enable all extensions to intercom you (except those explicitly denied)</td></tr><tr><td><strong>%snnn</strong>:</td><td>Explicitly allow extension nnn to intercom you (even if others are disabled)</td></tr><tr><td><strong>%s</strong>:</td><td>Disable all extensions from intercom you (except those explicitly allowed)</td></tr><tr><td><strong>%snnn</strong>:</td><td>Explicitly deny extension nnn to intercom you (even if generally enabled)</td></tr></table> Exclude Force if busy Group Description If selected, will not check if the device is in use before paging it. This means conversations can be interrupted by a page (depending on how the device handles it). This is useful for "emergency" paging groups  Include Intercom mode is currently disabled, it can be enabled in the Feature Codes Panel. Intercom prefix Modify Paging Group Page Group Page Group:  Paging Extension Paging and Intercom Paging is typically one way for announcements only. Checking this will make the paging duplex, allowing all phones in the paging group to be able to talk and be heard by all. This makes it like an "instant conference" Please enter a valid Paging Extension Please select at least one extension Provide a descriptive title for this Page Group. Select Device(s) to page. This is the phone that should be paged. In most installations, this is the same as the Extension. If you are configured to use "Users & Devices" this is the acutal Device and not the User.  Use Ctrl key to select multiple.. Submit Changes The number users will dial to page this group This module is for specific phones that are capable of Paging or Intercom. This section is for configuring group paging, intercom is configured through <strong>Feature Codes</strong>. Intercom must be enabled on a handset before it will allow incoming calls. It is possible to restrict incoming intercom calls to specific extensions only, or to allow intercom calls from all extensions but explicitly deny from specific extensions.<br /><br />This module should work with Aastra, Grandstream, Linksys/Sipura, Mitel, Polycom, SNOM , and possibly other SIP phones (not ATAs). Any phone that is always set to auto-answer should also work (such as the console extension if configured). User Intercom Allow User Intercom Disallow You can include or exclude this extension/device from being part of the default page group when creating or editing. Project-Id-Version: 2.5
Report-Msgid-Bugs-To: 
POT-Creation-Date: 2008-09-27 16:24+0200
PO-Revision-Date: 2008-11-06 14:52+0100
Last-Translator: Francesco Romano <francesco.romano@alteclab.it>
Language-Team: Italian
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
X-Poedit-Language: Italian
X-Poedit-Country: ITALY
 Aggiungi Gruppo Paging Interni in conflitto Gruppo Page Predefinito Gruppo Page Predefinito Elimina Gruppo %s Lista Apparati: Disattivato Duplex Ogni sistema PBX puo' avere un singolo Gruppo Page Predefinito. Se specificato, gli interni possono essere automaticamente aggiunti (o rimossi) da questo gruppo.<br />Impostando questo gruppo come predefinito verrà rimossa l'opzione dal gruppo corrente predefinito se esiste. Esempi di utilizzo: <br /><table><tr><td><strong>%snnn</strong>:</td><td>Chiama in Intercom l'interno nnn</td></tr><tr><td><strong>%s</strong>:</td><td>Permette a tutti di chiamare il proprio interno tramite intercom (eccetto quelli negati in maniera esplicita)</td></tr><tr><td><strong>%snnn</strong>:</td><td>Permette in maniera esplicita a nnn di poter chiamare il proprio interno tramite intercom (anche se tutti gli altri sono disabilitati)</td></tr><tr><td><strong>%s</strong>:</td><td>Nega a tutti di poter chiamare tramite intercom il proprio interno (eccetto quelli abilitati in maniera esplicita)</td></tr><tr><td><strong>%snnn</strong>:</td><td>Nega a nnn di poter chiamare tramite intercom il proprio interno (anche se in generale abilitato)</td></tr></table> Escludi Forza se occupato Descrizione Gruppo Se selezionato, non verrà controllato se gli apparati sono in uso. Questo significa che una conversazione potrebbe interrompersi durante una chiamata page (dipende in che modo l'apparato gestisce la situazione). Questo è utile per gruppi page di "emergenza". Includi La modalità Intercom in questo momento è disabilitata, può essere attivata tramite il Pannello Codici Servizi. Prefisso Intercom Modifica Gruppo Paging Gruppo Page Gruppo Page: Interno Paging Paging e Intercom Il Paging di solito è con l'audio solo andata, utilizzato per gli annunci. Selezionando questa opzione il paging sarà in modalità duplex, permettendo a tutti i telefoni del gruppo di parlare tra loro. Come se fosse una "conferenza instantanea" Prego immettere un Interno Paging valido Prego selezionare almeno un interno Inserire una descrizione per questo Gruppo Page. Selezionare gli Apparati da includere nel gruppo Page. In generale l'apparato corrisponde all'Interno. Se si utilizza la modalità "Utenti & Apparati" (Users & Devices), questo dovrà essere il numero dell'apparato. Utilizzare il tasto Ctrl per selezioni multiple. Conferma Cambiamenti Il numero che gli utenti dovranno chiamare per effettuare il page su questo gruppo Questo modulo è valido per i telefoni che sono in grado di utilizzare il Paging o l'Intercom. Questa sezione serve a configurare il gruppo Page, l'intercom si può configurare attraverso i <strong>Codici Servizi</strong>. L'Intercom deve essere abilitato nell'apparato prima di poter accettare le chiamate in arrivo. E' possibile restringere le chiamate intercom ad interni specifici, o permettere le chiamate intercom da tutti gli interni e negare in maniera esplicita solo alcuni interni.<br /><br />Al momento sono supportati i telefoni Aastra, Grandstream,  Linksys/Sipura, Mitel, Polycom, Snom, Thomson e forse altri telefoni a standard SIP (non ATA). Tutti i telefoni che hanno comunque la possibilità di utilizzare l'auto-risposta dovrebbero funzionare (come anche la console se configurata). Permette Intercom Non permette Intercom Puoi includere o escludere questo interno/apparato dal far parte del gruppo page predefinito quando si crea o modifica. 
��            )         �     �     �     �  f   �  W   W  �   �     S     j  1  }     �     �  �  �  9   �  W   �  2   Q	     �	     �	     �	     �	     �	     �	  O   �	  $   3
  0   X
  a   �
  _   �
     K  �   Q  �   '  �   �  =  ~     �     �     �  k     P   s  �   �     `     u  t  �     �       �    6   �  o     8   �     �     �     �  $   �          +  a   :  /   �  )   �  �   �  �   x  
     �     �   �  �   V           	                                                                                                                      
       (pick destination) Add Custom Destination Add Custom Extension Brief Description that will be published to modules when showing destinations. Example: My Weather App Brief description that will be published in the Extension Registry about this extension Choose un-identified destinations on your system to add to the Custom Destinaion Registry. This will insert the chosen entry into the Custom Destination box above. Conflicting Extensions Custom Destination Custom Destinations allows you to register your custom destinations that point to custom dialplans and will also 'publish' these destinations as available destinations to other modules. This is an advanced feature and should only be used by knowledgeable users. If you are getting warnings or errors in the notification panel about CUSTOM destinations that are correct, you should include them here. The 'Unknown Destinations' chooser will allow you to choose and insert any such destinations that the registry is not aware of into the Custom Destination field. Custom Extension Custom Extension:  Custom Extensions provides you with a facility to register any custom extensions or feature codes that you have created in a custom file and FreePBX doesn't otherwise know about them. This allows the Extension Registry to be aware of your own extensions so that it can detect conflicts or report back information about your custom extensions to other modules that may make use of the information. You should not put extensions that you create in the Misc Apps Module as those are not custom. DUPLICATE Destination: This destination is already in use DUPLICATE Destination: This destination is in use or potentially used by another module DUPLICATE Extension: This extension already in use Delete Description Destination Quick Pick Edit Custom Destination Edit Custom Extension Edit:  Invalid Destination, must not be blank, must be formatted as: context,exten,pri Invalid Extension, must not be blank Invalid description specified, must not be blank More detailed notes about this destination to help document it. This field is not used elsewhere. More detailed notes about this extension to help document it. This field is not used elsewhere. Notes This is the Custom Destination to be published. It should be formatted exactly as you would put it in a goto statement, with context, exten, priority all included. An example might look like:<br />mycustom-app,s,1 This is the Custom Destination to be published. It should be formatted exactly as you would putit in a goto statement, with context, exten, priority all included. An example might look like:<br />mycustom-app,s,1 This is the Extension or Feature Code you are using in your dialplan that you want the FreePBX Extension Registry to be aware of. Project-Id-Version: 1.3
Report-Msgid-Bugs-To: 
POT-Creation-Date: 2007-04-19 21:45+0100
PO-Revision-Date: 2008-01-16 16:38+0100
Last-Translator: Alexander Kozyrev <ceo@postmet.com>
Language-Team: Russian <faq@postmet.com>
MIME-Version: 1.0
Content-Type: text/plain; charset=iso-8859-5
Content-Transfer-Encoding: 8bit
 (������� ����������) �������� ����������� ���������� �������� ������ ����� �������� ��������, ������� �������� ��� ������ ����� ����������. ��������: ������ ���������������� �������� �������� �������� ����� ������, ������� ����� �������������� � �������� �������. �������� �������������������� ���������� ����� ������� ����� �������� � ������� ��������������. ��� ���� ��� �������� ������ �������� � ���� �������������� ������������� ������ �������������� ����������� ���������� ���� ����������� �������������� ����������� ��������, ������� ������������ � ��� ���� ���� � ���������� ���������� ��� ��������������� �� ��� �� ������ �������. ��� ����� ����� ����������� �����, � ����� �������������� ������ �������� ��������������, ������� �������� ��� ��� ����� �������. ���� ����� �������������� ��� ��������� � ������ ��������� ������� �� ������ CUSTOM �����������, �� ��� � �������, �� ������ ��� ������ ������������. ����� ���������� ������ � ��������� �������� ������������� ����� ��������� ��� 'Custom Applications' ��� ��������� �������� ����� ������� � '���������� �����������' ������ ����� ������ �����: ������ ������ �������� �������������� �����-���� ����������� ������ ��������, ������� ��������� � custom ������, � FreePBX �� ����� ������� � ���. ��� ���� ����������� ��������� ������� ���������� ������� ����� �������, ����� ������������� ��������� � ����������� �������� (��� ������ ��������) � �������� � ��� � ������ ������, ������� ����� ������������ ��� ����������. �� �� ������ ��������� ��� ������ ������ � ������ Misc Apps ��� ��� ��� ����� �������. �ñ����� � ����������: ��� ���������� ��� ������������ �ñ����� � ����������: ��� ���������� ��� ������������ ��� ������������ ����� ���� ������������ � ������ ������ �ñ����� ����������� ������: ���� ����� ��� ������������ ������� �������� ���������� ������ �������� ������������� ����������� ���������� ������������� ������ ����� �������������: �������� ����������, �� ������ ���� ������, � ������ ���� � �������: ��������, ������� ���������. �������� ���������� �����, �� ����� ���� ������ �������� ��������. �� ������ ���� ������. ����� ��������� ���������, ������� ������� ��������������� ��� ����������, ����� �� �����. ��� ���� ������ ����� �� ������������. ����� ��������� ���������� �� ���� ������, ������� ������� ��������������� ������, ����� �� �����. ��� ���� ������ ����� �� ������������. ���������� ��� ���������� ������������� ������������ ����������. ��� �������� ���������� ��������� ��������, ���������� ����� � ���������. ��������:<br />mycustom-app,s,1 ����� ������� ������ ����������. ��� ������ ����� ������ ��������������� GoTo � ������������ ��������� ���������, ���������, ����������. ��������:<br />mycustom-app,s,1 ��� ������ ����� ��� ��� ��������, ������� ����� �������������� � ����� ���� �����, ��� ����� ��� � ����� �������� ������� FreePBX. 
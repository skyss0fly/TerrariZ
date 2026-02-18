# TerrariZ
TerrariZ is a server software written in php (IN Development)


# Current Features:
- Logger Class,
- Packet Handler, Packet Interface Classes.
the following Packets Have Been Implemented:
```
    1  => PlayerLoginRequestPacket::class,
    4  => PlayerCreationPacket::class,
    5 => PlayerInventorySlotPacket::class,
    38 => PasswordVerificationPacket::class, (still Fixing Some Bugs)
    42 => PlayerManaPacket::class, (still testing)
    68 => SyncUUIDPacket::class,
```
# The following Packets Have been Fully Implemented:
> # 1 (Client Join Request)
> this Packet checks if the server has a password enabled (in Config.yml), if true, then it will send the Packet 37 to the client. if false, it will send 3 to client.

# The following Packets have been Partially Implemented:
> # 38 (Client Password Packet)
> This packet checks the Validity of the Password (in Config.yml), if it matches up, it will send Packet id 3 to the Client, if not, it will send the Packet id 2 (Disconnect Packet) to the client.
> this Packet is not fully complete as it does not send the correct formatting for the kick message as the client will recieve the text "valid Password***************"


# Todo:

- Rewrite Main Server Thread to support Asynchronous Threads to support tasks like Garbage Collection, Console Threads and Multiple tasks at once.
- Complete Rest of Initial Server and Client Handshake
- Write a world generation system similar to that of [TShock](https://github.com/Pryaxis/Tshock)
- Write a world reading system to Load the correct world information to the client.


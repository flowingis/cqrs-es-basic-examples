# cqrs-es-basic-examples

# Requisiti

- Docker

# Avvio applicazione

Dalla root del progetto (`path/to/cqrs-es-basic-examples`) lanciare:
- `docker-compose up -d`

Per controllare se l'applicazione sta funzionando correttamente:
- `docker-compose exec php php Examples/CommandHandling/commandHandlingSolution.php`

Dovresti vedere il messaggio:
- `ID : 234-ae2, First Name: mario, Last Name: rossi`

# Documentazione Broadway

## Documentazione generale
https://broadway.github.io/broadway/

## Esempio di event handling
https://github.com/broadway/broadway/blob/master/examples/event-handling/event-handling.php

## Esempio di command handling
https://github.com/broadway/broadway/blob/master/examples/command-handling/command-handling.php

## Altri esempi
https://github.com/broadway/broadway/tree/master/examples

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

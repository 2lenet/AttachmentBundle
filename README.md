# AttachmentBundle

## Installation

`composer req 2lenet/attachment-bundle`

Add to your config/routes.yaml ( no flex reciepe for the moment ).

```yaml
lle_attachment:
    resource: "@LleAttachmentBundle/Resources/config/routing/routes.yaml"
```


## Embed controller in a template

```twig
{{ render(controller('lle.attachment', {'item': item)) }}
```

Like that you can use "lle.attachment" action with EasyAdminPlusBundle:

```yaml
show:
    title: title.examen.show
    fields:
        - { type: 'tab', id: 'documents', label: 'tab.documents', action: 'lle.attachment'}
```

Information: the lle.attachment is a alias of lle.attachment.show.action

## Easy use

render the widget for upload:
```twig
{{ render_attachment(entity) }}
```

render the list (ul) for download:
```twig
{{ list_attachment(entity) }}
```


You can use several attachment on the same template

```twig
{{ render_attachment(item) }}

{%  for examen in item.examens  %}
    <h3>{{ examen }}</h3>
    {{ render_attachment(examen) }}
{%  endfor %}
```

## Configurations

with default value

```yaml
framework:
    translator:
        fallbacks: ['fr']

lle_attachment:
    directory: data/attachment #directory of files is registred
    show_list: false #show or not show the list of files (table) with uploader widget
```

Thanks to use

## pre load for list of entities:

If you list 20 entities with 20 call of {{ list_attachment(entity) }} you want 1 request not 20:

call 
```php
<?php
$this->attachmentManager->load(MyEntityClass::class, $ids);
```

exemple with EasyAdmin
```php
<?php

namespace App\EventListener;

use App\Entity\Rapport;
use Lle\AttachmentBundle\Service\AttachmentManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Symfony\Component\EventDispatcher\GenericEvent;

class RapportSubscriber implements EventSubscriberInterface
{
    private $attachmentManager;

    public function __construct(AttachmentManager $attachmentManager)
    {
        $this->attachmentManager = $attachmentManager;
    }


    public static function getSubscribedEvents()
    {
        return [
            EasyAdminEvents::POST_LIST => 'onPostList',
        ];
    }


    public function onPostList(GenericEvent $event)
    {
        if($event->getArgument('entity')['class'] === Rapport::class){
            $ids = [];
            foreach($event->getSubject()->getCurrentPageResults() as $item){
                $ids[] = $item->getId();
            }
            $this->attachmentManager->load(Rapport::class, $ids);
        }
    }



}

```
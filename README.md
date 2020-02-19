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
{{ render(controller('lle.attachment', {'item': item})) }}
```
Or with field if you want only an group of attachment
```twig
{{ render(controller('lle.attachment', {'item': item, options: {'field': 'pdf'}})) }}
```

Like that you can use "lle.attachment" action with EasyAdminPlusBundle:

```yaml
show:
    title: title.examen.show
    fields:
        - { type: 'tab', id: 'documents', label: 'tab.documents', action: 'lle.attachment'}
```

Or with field if you want only an group of attachment
 
```yaml
show:
    title: title.examen.show
    fields:
        - { type: 'tab', id: 'documents', label: 'tab.documents', action: 'lle.attachment', options: {'field': 'pdf'}}
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
    need_confirm_remove: true #modal for confirm delete or no
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

## add uploaded file in form

- Add UploadedFileAttachmentInterface with UploadedFileAttachmentTrait at your entity
```php
use Lle\AttachmentBundle\UploadedFileAttachmentInterface;
use Lle\AttachmentBundle\UploadedFileAttachmentTrait;
```

- In your entity form add 
```php
$builder->add('uploadedFilesAttachment', FileType::class, ['multiple'=> true]);
// or (and) $builder->add('uploadedFileAttachment', FileType::class, ['multiple'=> false]);
$builder->add('uploadedFilesAttachmentField'); //optional (default is null)
```

- Now you have to call addFileByEntity after persist (entity identification have to be create)

```php
$this->attachmentManager->addFileByEntity($entity);
```

- here an simple exemple with EasyAdmin:

```php

<?php

namespace App\EventListener;


use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Lle\AttachmentBundle\Service\AttachmentManager;
use Lle\AttachmentBundle\UploadedFileAttachmentInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class AttachmentSubscriber implements EventSubscriberInterface
{

    private $attachmentManager;

    public function __construct(AttachmentManager $attachmentManager){
        $this->attachmentManager = $attachmentManager;
    }

    public function addFile(GenericEvent $event){
        if($event->getSubject() instanceof UploadedFileAttachmentInterface){
            $this->attachmentManager->addFileByEntity($event->getSubject());
        }
    }


    public static function getSubscribedEvents()
    {
        return [
            EasyAdminEvents::POST_PERSIST => 'addFile',
            EasyAdminEvents::POST_UPDATE => 'addFile'
        ];
    }
}

```

- if you want you can defin the default filed in entity
```php
public function getUploadedFilesAtachmentField(): ?string{
    return 'uploaded';
}
```

- For validation use Symfony validator annotations or validator in form  (Advice: if you use validator annotation create your own Trait)
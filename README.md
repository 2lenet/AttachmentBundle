# AttachmentBundle

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

Information: the lle.attahcment is a alias of lle.attachment.show.action

## Easy use

```twig
{{ render_attachment(entity) }}
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
lle_attachment:
    directory: data/attachment #directory of files is registred
    show_list: false #show or not show the list of files
```

Thanks to use